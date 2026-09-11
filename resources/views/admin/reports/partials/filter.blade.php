@php
    $route = $route ?? url()->current();
    $ranges = $ranges ?? [];
    $selects = $selects ?? [];
    $showRange = $showRange ?? true;
    $showSearch = $showSearch ?? false;
    $searchPlaceholder = $searchPlaceholder ?? 'Search...';
    $exportReport = $exportReport ?? null;
    $reportTitle = $reportTitle ?? 'Report';
    $rangeLabel = $rangeLabel ?? null;
    $from = $from ?? null;
    $to = $to ?? null;
@endphp

{{-- Print-only header --}}
<div class="a-print-header">
    <h2><span class="a-print-brand">Evan Store</span> — {{ $reportTitle }}</h2>
    <div class="a-print-meta">
        <span><strong>Date Range:</strong> {{ $rangeLabel ?? 'All Time' }}</span>
        @foreach ($selects as $s)
            @if (request($s['name']) !== null && request($s['name']) !== '')
                <span><strong>{{ $s['label'] }}:</strong> {{ $s['options'][request($s['name'])] ?? request($s['name']) }}</span>
            @endif
        @endforeach
        @if (request('search'))
            <span><strong>Search:</strong> {{ request('search') }}</span>
        @endif
        <span><strong>Generated:</strong> {{ now()->format('d M Y, h:i A') }}</span>
    </div>
</div>

<div class="a-card a-report-filter mb-3">
    <div class="a-card-body">
        <form method="GET" action="{{ $route }}" class="a-filter-form">
            <div class="a-filter-grid">
                @if ($showRange)
                    <div class="a-filter-field">
                        <label class="form-label">Date Range</label>
                        <select name="range" class="form-select">
                            @foreach ($ranges as $key => $label)
                                <option value="{{ $key }}"
                                    {{ (string) request('range', 'all') === (string) $key ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="a-filter-field">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control"
                            value="{{ request('from_date', $from ? $from->toDateString() : '') }}">
                    </div>
                    <div class="a-filter-field">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control"
                            value="{{ request('to_date', $to ? $to->toDateString() : '') }}">
                    </div>
                @endif

                @foreach ($selects as $s)
                    <div class="a-filter-field">
                        <label class="form-label">{{ $s['label'] }}</label>
                        <select name="{{ $s['name'] }}" class="form-select">
                            @foreach ($s['options'] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ (string) request($s['name']) === (string) $val ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach

                @if ($showSearch)
                    <div class="a-filter-field">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="{{ $searchPlaceholder }}"
                            value="{{ request('search') }}">
                    </div>
                @endif
            </div>

            <div class="a-filter-actions">
                <button type="submit" class="btn btn-theme"><i class="ri-filter-2-line"></i> Filter</button>
                <a href="{{ $route }}" class="btn btn-outline-secondary"><i class="ri-refresh-line"></i> Reset</a>

                @if ($exportReport)
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-download-2-line"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item"
                                    href="{{ route('admin.reports.export', array_merge(request()->query(), ['report' => $exportReport])) }}">
                                    <i class="ri-file-excel-2-line me-1"></i> Excel (.xlsx)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                    href="{{ route('admin.reports.export', array_merge(request()->query(), ['report' => $exportReport, 'format' => 'csv'])) }}">
                                    <i class="ri-file-text-line me-1"></i> CSV
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif

                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="ri-printer-line"></i> Print
                </button>
            </div>
        </form>
    </div>
</div>
