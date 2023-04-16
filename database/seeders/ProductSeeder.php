<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ProductMasterList;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'product_id' => '4450',
                'types' => 'Smartphone',
                'brand'  => 'Apple',
                'model' => 'iPhone SE',
                'capacity'  => '2GB/16GB',
                'quantity'   => 13
            ],
            [
                'product_id' => '4768',
                'types' => 'Smartphone',
                'brand'  => 'Apple',
                'model' => 'iPhone SE',
                'capacity'  => '2GB/32GB',
                'quantity'   => 30
            ],
            [
                'product_id' => '4451',
                'types' => 'Smartphone',
                'brand'  => 'Apple',
                'model' => 'iPhone SE',
                'capacity'  => '2GB/64GB',
                'quantity'   => 20
            ],
            [
                'product_id' => '4574',
                'types' => 'Smartphone',
                'brand'  => 'Apple',
                'model' => 'iPhone SE',
                'capacity'  => '2GB/128GB',
                'quantity'   => 16
            ],
            [
                'product_id' => '6039',
                'types' => 'Smartphone',
                'brand'  => 'Apple',
                'model' => 'iPhone SE (2020)',
                'capacity'  => '3GB/64GB',
                'quantity'   => 18
            ]
        ];

        foreach ($products as $product) {
            ProductMasterList::create([
                'product_id' => $product['product_id'],
                'types'      => $product['types'],
                'brand'      => $product['brand'],
                'model'      => $product['model'],
                'capacity'   => $product['capacity'],
                'quantity'   => $product['quantity'],
            ]);
        }
    }
}
