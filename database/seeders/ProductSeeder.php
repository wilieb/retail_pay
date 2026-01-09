<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::upsert([
            [
                'name' => 'Unga wa Jogoo',
                'sku' => 'UG-0001',
                'quantity' => 120,
                'units' => 'kgs',
                'cost_per_unit' => 105.00,
                'retail_price' => 120.00,
                'status' => 'in stock',
            ],
            [
                'name' => 'Mumias Sugar',
                'sku' => 'SG-0002',
                'quantity' => 90,
                'units' => 'kgs',
                'cost_per_unit' => 170.00,
                'retail_price' => 190.00,
                'status' => 'in stock',
            ],
            [
                'name' => 'Cooking Oil',
                'sku' => 'CO-0003',
                'quantity' => 180,
                'units' => 'lts',
                'cost_per_unit' => 280.00,
                'retail_price' => 325.00,
                'status' => 'in stock',
            ],
            [
                'name' => 'Milk',
                'sku' => 'MK-0004',
                'quantity' => 200,
                'units' => 'lts',
                'cost_per_unit' => 55.00,
                'retail_price' => 65.00,
                'status' => 'in stock',
            ],
            [
                'name' => 'Bread',
                'sku' => 'BR-0005',
                'quantity' => 150,
                'units' => 'pcs',
                'cost_per_unit' => 55.00,
                'retail_price' => 65.00,
                'status' => 'in stock',
            ],
            [
                'name' => 'Rice',
                'sku' => 'RC-0006',
                'quantity' => 100,
                'units' => 'kgs',
                'cost_per_unit' => 260.00,
                'retail_price' => 300.00,
                'status' => 'in stock',
            ],
            [
                'name' => 'Laundry Soap',
                'sku' => 'SP-0007',
                'quantity' => 180,
                'units' => 'pcs',
                'cost_per_unit' => 45.00,
                'retail_price' => 60.00,
                'status' => 'in stock',
            ],
            [
                'name' => 'Sugarcane Juice',
                'sku' => 'SJ-0008',
                'quantity' => 60,
                'units' => 'lts',
                'cost_per_unit' => 160.00,
                'retail_price' => 240.00,
                'status' => 'in stock',
            ],
            [
                'name' => 'Charcoal',
                'sku' => 'CH-0009',
                'quantity' => 40,
                'units' => 'sack',
                'cost_per_unit' => 650.00,
                'retail_price' => 800.00,
                'status' => 'in stock',
            ],
            [
                'name' => 'Drinking Water',
                'sku' => 'DW-0010',
                'quantity' => 300,
                'units' => 'lts',
                'cost_per_unit' => 25.00,
                'retail_price' => 35.00,
                'status' => 'in stock',
            ],
        ], ['sku'], [
            'name',
            'quantity',
            'units',
            'cost_per_unit',
            'retail_price',
            'status',
        ]);
    }
}
