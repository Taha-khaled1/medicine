<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            [
                'name' => 'Paracetamol',
                'price' => 10.50,
                'quantity' => 100,
                'expiry_date' => Carbon::now()->addYears(2),
                'type' => 'box',
                'subunits_per_unit' => 10,
                'subunits_count' => 1000,
                'scientific_form' => '500mg tablet'
            ],
            [
                'name' => 'Amoxicillin',
                'price' => 25.75,
                'quantity' => 50,
                'expiry_date' => Carbon::now()->addMonths(18),
                'type' => 'strip',
                'subunits_per_unit' => 12,
                'subunits_count' => 600,
                'scientific_form' => '500mg capsule'
            ],
            [
                'name' => 'Omeprazole',
                'price' => 15.25,
                'quantity' => 75,
                'expiry_date' => Carbon::now()->addMonths(24),
                'type' => 'box',
                'subunits_per_unit' => 14,
                'subunits_count' => 1050,
                'scientific_form' => '20mg capsule'
            ],
            [
                'name' => 'Ibuprofen',
                'price' => 12.99,
                'quantity' => 60,
                'expiry_date' => Carbon::now()->subMonths(1), // Expired medicine
                'type' => 'box',
                'subunits_per_unit' => 10,
                'subunits_count' => 600,
                'scientific_form' => '400mg tablet'
            ],
            [
                'name' => 'Cetirizine',
                'price' => 8.50,
                'quantity' => 30,
                'expiry_date' => Carbon::now()->addMonths(2), // Expiring soon
                'type' => 'strip',
                'subunits_per_unit' => 10,
                'subunits_count' => 300,
                'scientific_form' => '10mg tablet'
            ]
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }
    }
}
