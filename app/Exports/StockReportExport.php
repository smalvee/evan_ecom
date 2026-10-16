<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Formatted Stock Report Excel export.
 *
 * Produces a professional two-level grouped header (Opening / Purchase / Sales /
 * Closing, each with Qty/Rate/Value sub-columns) with the same section colour
 * language as the web report, a title + metadata block, number formats, frozen
 * panes and an autofilter — while keeping every cell numeric so the sheet can
 * be filtered, sorted and pivoted.
 */
class StockReportExport implements FromArray, WithTitle, WithEvents, WithStrictNullComparison
{
    protected array $items;

    protected array $meta;

    protected const SECTIONS = [
        // [group label, header fill, body fill, first col, last col]
        ['Opening Stock', '475569', 'F1F5F9', 'C', 'E'],
        ['Purchase', '047857', 'ECFDF5', 'F', 'H'],
        ['Sales', 'B45309', 'FFFBEB', 'I', 'K'],
        ['Closing Stock', '4338CA', 'EEF2FF', 'L', 'N'],
    ];

    public function __construct(array $items, array $meta)
    {
        $this->items = $items;
        $this->meta = $meta;
    }

    public function title(): string
    {
        return 'Stock Report';
    }

    public function array(): array
    {
        $matrix = [];

        // Metadata block.
        $matrix[] = ['Stock Report'];
        $matrix[] = ['Period: ' . ($this->meta['range_label'] ?? 'All Time')];

        $filters = [];
        if (!empty($this->meta['category'])) {
            $filters[] = 'Category: ' . $this->meta['category'];
        }
        if (!empty($this->meta['brand'])) {
            $filters[] = 'Brand: ' . $this->meta['brand'];
        }
        if (!empty($this->meta['search'])) {
            $filters[] = 'Search: ' . $this->meta['search'];
        }
        $matrix[] = $filters ? [implode('   |   ', $filters)] : ['Filters: All'];

        $matrix[] = ['Generated: ' . ($this->meta['generated_at'] ?? now()->format('d M Y, h:i A'))];

        // Group header (row 5).
        $matrix[] = ['SKU No', 'SKU Name', 'Opening Stock', '', '', 'Purchase', '', '', 'Sales', '', '', 'Closing Stock', '', ''];

        // Sub-header (row 6) — full column labels for the autofilter.
        $matrix[] = ['SKU No', 'SKU Name', 'Opening Qty', 'Opening Rate', 'Opening Value', 'Purchase Qty', 'Purchase Rate', 'Purchase Value', 'Sales Qty', 'Sales Rate', 'Sales Value (COGS)', 'Closing Qty', 'Closing Rate', 'Closing Value'];

        foreach ($this->items as $v) {
            $s = $v->stock;
            $matrix[] = [
                $this->safeText($v->sku),
                $this->safeText($v->product),
                (float) $s['opening_qty'],
                (float) $s['opening_rate'],
                (float) $s['opening_value'],
                (float) $s['purchase_qty'],
                (float) $s['purchase_rate'],
                (float) $s['purchase_value'],
                (float) $s['sales_qty'],
                (float) $s['sales_rate'],
                (float) $s['sales_value'],
                (float) $s['closing_qty'],
                (float) $s['closing_rate'],
                (float) $s['closing_value'],
            ];
        }

        return $matrix;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->formatSheet($event->sheet->getDelegate());
            },
        ];
    }

    protected function formatSheet(Worksheet $sheet): void
    {
        $lastRow = max(6, (int) $sheet->getHighestRow());
        $hasData = $lastRow >= 7;

        // Title + metadata.
        $sheet->getStyle('A1:N1')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('0F172A');
        $sheet->getStyle('A2:N4')->getFont()->setSize(10)->getColor()->setARGB('64748B');

        foreach (range(1, 4) as $row) {
            $sheet->mergeCells("A{$row}:N{$row}");
        }

        // Merge each group header across its three sub-columns.
        foreach (self::SECTIONS as $section) {
            $sheet->mergeCells($section[3] . '5:' . $section[4] . '5');
        }

        // Group header fills (dark + white bold text).
        foreach (self::SECTIONS as $section) {
            $range = $section[3] . '5:' . $section[4] . '5';
            $style = $sheet->getStyle($range);
            $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($section[1]);
            $style->getFont()->setBold(true)->getColor()->setARGB('FFFFFF');
            $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        }

        // SKU No / SKU Name header cells.
        $sheet->getStyle('A5:B6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E2E8F0');
        $sheet->getStyle('A5:B6')->getFont()->setBold(true)->getColor()->setARGB('0F172A');
        $sheet->getStyle('A5:B6')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Sub-header row (row 6) — light tint + bold.
        foreach (self::SECTIONS as $section) {
            $range = $section[3] . '6:' . $section[4] . '6';
            $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($section[2]);
            $sheet->getStyle($range)->getFont()->setBold(true);
        }

        // Data body fills.
        if ($hasData) {
            foreach (self::SECTIONS as $section) {
                $range = $section[3] . '7:' . $section[4] . $lastRow;
                $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($section[2]);
            }
        }

        // Thin borders across the header + data range.
        $sheet->getStyle("A5:N{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'E2E8F0']],
            ],
        ]);

        if ($hasData) {
            $this->applyNumberFormats($sheet, $lastRow);
        }

        // Column widths.
        $sheet->getColumnDimension('A')->setWidth(16);
        $sheet->getColumnDimension('B')->setWidth(30);
        foreach (range('C', 'N') as $col) {
            $sheet->getColumnDimension($col)->setWidth(13);
        }

        // Freeze the SKU columns and header rows.
        $sheet->freezePane('C7');

        // Autofilter over the complete data range.
        if ($hasData) {
            $sheet->setAutoFilter('A6:N' . $lastRow);
        }
    }

    protected function applyNumberFormats(Worksheet $sheet, int $lastRow): void
    {
        // Qty columns: C, F, I, L
        foreach (['C', 'F', 'I', 'L'] as $col) {
            $sheet->getStyle($col . '7:' . $col . $lastRow)->getNumberFormat()->setFormatCode('#,##0.##');
        }

        // Rate columns: D, G, J, M
        foreach (['D', 'G', 'J', 'M'] as $col) {
            $sheet->getStyle($col . '7:' . $col . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
        }

        // Value columns: E, H, K, N
        foreach (['E', 'H', 'K', 'N'] as $col) {
            $sheet->getStyle($col . '7:' . $col . $lastRow)->getNumberFormat()->setFormatCode('#,##0.##');
        }
    }

    /**
     * Prevent spreadsheet formula injection from user-controlled text.
     */
    protected function safeText($value): string
    {
        $value = (string) $value;

        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $value;
        }

        return $value;
    }
}
