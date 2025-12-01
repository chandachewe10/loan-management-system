<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxBand;

class TaxBandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default ZRA Zambia Tax Bands (PAYE structure)
        // These are example rates - should be updated to match current ZRA rates
        
        $taxBands = [
            [
                'name' => 'Tax Free Threshold',
                'min_income' => 0,
                'max_income' => 4500,
                'tax_rate' => 0,
                'fixed_amount' => 0,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'First Band',
                'min_income' => 4500,
                'max_income' => 6000,
                'tax_rate' => 25,
                'fixed_amount' => 0,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Second Band',
                'min_income' => 6000,
                'max_income' => 9000,
                'tax_rate' => 30,
                'fixed_amount' => 375, // 25% of (6000-4500) = 375
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Third Band',
                'min_income' => 9000,
                'max_income' => null,
                'tax_rate' => 37.5,
                'fixed_amount' => 1275, // 375 + 30% of (9000-6000) = 1275
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($taxBands as $band) {
            TaxBand::updateOrCreate(
                [
                    'name' => $band['name'],
                    'min_income' => $band['min_income'],
                ],
                $band
            );
        }
    }
}

