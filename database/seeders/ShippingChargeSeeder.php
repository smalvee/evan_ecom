<?php

namespace Database\Seeders;

use App\Models\ShippingCharge;
use Illuminate\Database\Seeder;

class ShippingChargeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('districts.list') as $district) {
            ShippingCharge::firstOrCreate(
                ['district' => $district],
                [
                    'location' => $district,
                    'amount' => $district === 'Dhaka' ? 70 : 130,
                ],
            );
        }

        // Remove legacy rows that predate per-district charges
        ShippingCharge::whereNull('district')->delete();
    }
}
