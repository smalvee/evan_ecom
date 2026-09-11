<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Converts purchase/return monetary columns from INT to DECIMAL(12,2) so
     * fractional costs are stored accurately.
     *
     * Each statement is guarded so the migration never fails if a table/column
     * is missing (e.g. when the purchase migrations have not been applied yet).
     */
    public function up(): void
    {
        $this->modify('purchases', 'total', 'DECIMAL(12,2) NULL');

        $this->modify('purchase_items', 'unit_cost', 'DECIMAL(12,2) NOT NULL DEFAULT 0');
        $this->modify('purchase_items', 'discount', 'DECIMAL(12,2) NOT NULL DEFAULT 0');
        $this->modify('purchase_items', 'profit_margin', 'DECIMAL(12,2) NOT NULL DEFAULT 0');
        $this->modify('purchase_items', 'selling_price', 'DECIMAL(12,2) NOT NULL DEFAULT 0');

        $this->modify('purchase_returns', 'return_amount', 'DECIMAL(12,2) NOT NULL DEFAULT 0');

        $this->modify('purchase_return_items', 'unit_cost', 'DECIMAL(12,2) NOT NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->modify('purchases', 'total', 'INT(11) NULL');

        $this->modify('purchase_items', 'unit_cost', 'INT(50) NOT NULL DEFAULT 0');
        $this->modify('purchase_items', 'discount', 'INT(50) NOT NULL DEFAULT 0');
        $this->modify('purchase_items', 'profit_margin', 'INT(50) NOT NULL DEFAULT 0');
        $this->modify('purchase_items', 'selling_price', 'DECIMAL(10,2) NOT NULL DEFAULT 0');

        $this->modify('purchase_returns', 'return_amount', 'INT(11) NOT NULL DEFAULT 0');

        $this->modify('purchase_return_items', 'unit_cost', 'INT(11) NOT NULL DEFAULT 0');
    }

    /**
     * Safely modify a column only when its table and column exist.
     */
    protected function modify(string $table, string $column, string $definition): void
    {
        if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
            DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` {$definition}");
        }
    }
};
