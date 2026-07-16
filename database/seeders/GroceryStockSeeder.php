<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Inventory;

class GroceryStockSeeder extends Seeder
{
    public function run(): void
    {

        $data = [

            'Baby' => [

                [
                    'product' => 'Baby Shampoo',
                    'variant' => 'Bzu Bzu Baby Shampoo',
                    'unit' => 'bottle',
                    'stock' => 2,
                    'aliases' => [
                        'bzu',
                        'bzu bzu',
                        'bzu shampoo'
                    ]
                ],

                [
                    'product' => 'Baby Shampoo',
                    'variant' => 'Drypers Baby Shampoo',
                    'unit' => 'bottle',
                    'stock' => 5,
                    'aliases' => [
                        'drypers shampoo'
                    ]
                ],

                [
                    'product' => 'Diaper',
                    'variant' => 'Drypers XL',
                    'unit' => 'pack',
                    'stock' => 1,
                    'minimum_stock' => 2,
                    'aliases' => [
                        'drypers',
                        'diaper',
                        'xl diaper'
                    ]
                ],

                [
                    'product' => 'Milk Powder',
                    'variant' => 'Lactogrow 600g',
                    'unit' => 'box',
                    'stock' => 3,
                    'aliases' => [
                        'lactogrow'
                    ]
                ],

            ],

            'Cleaning' => [

                [
                    'product' => 'Dishwashing Detergent',
                    'variant' => 'Sunlight Detergent',
                    'unit' => 'bottle',
                    'stock' => 3,
                    'aliases' => [
                        'sunlight'
                    ]
                ],

                [
                    'product' => 'Disinfectant',
                    'variant' => 'Dettol 5L',
                    'unit' => 'bottle',
                    'stock' => 2,
                    'aliases' => [
                        'dettol'
                    ]
                ],

                [
                    'product' => 'Floor Cleaner',
                    'variant' => 'Dettol Floor Cleaner',
                    'unit' => 'bottle',
                    'stock' => 4,
                    'aliases' => [
                        'floor cleaner'
                    ]
                ],

                [
                    'product' => 'Laundry Detergent',
                    'variant' => 'Top Detergent',
                    'unit' => 'bag',
                    'stock' => 1,
                    'aliases' => [
                        'top'
                    ]
                ],

                [
                    'product' => 'Laundry Detergent',
                    'variant' => 'Dynamo Detergent',
                    'unit' => 'bag',
                    'stock' => 0,
                    'aliases' => [
                        'dynamo'
                    ]
                ],

            ],

            'Drinks' => [

                [
                    'product' => 'Fresh Milk',
                    'variant' => 'Lotus Fresh Milk 1L',
                    'unit' => 'bottles',
                    'stock' => 12,
                    'aliases' => [
                        'lotus',
                        'lotus milk',
                        'fresh milk'
                    ]
                ],

            ],

            'Household' => [

                [
                    'product' => 'Compact Tissue',
                    'variant' => 'Cutie Compact Tissue',
                    'unit' => 'pack',
                    'stock' => 4,
                    'aliases' => [
                        'cutie tissue'
                    ]
                ],

                [
                    'product' => 'Wet Tissue',
                    'variant' => 'Bzu Bzu Wet Tissue 30pcs',
                    'unit' => 'pack',
                    'stock' => 5,
                    'aliases' => [
                        'bzu wet tissue'
                    ]
                ],

                [
                    'product' => 'Wet Tissue',
                    'variant' => 'Poomsoft Wet Tissue 80pcs',
                    'unit' => 'pack',
                    'stock' => 10,
                    'aliases' => [
                        'poomsoft'
                    ]
                ],

                [
                    'product' => 'Wet Tissue',
                    'variant' => 'Other Brand Wet Tissue 30pcs',
                    'unit' => 'pack',
                    'stock' => 5,
                    'aliases' => [
                        'wet tissue'
                    ]
                ],

            ],

        ];



        foreach ($data as $categoryName => $items) {


            $category = Category::firstOrCreate([
                'name' => $categoryName
            ]);



            foreach ($items as $item) {


                $product = Product::firstOrCreate([
                    'category_id' => $category->id,
                    'name' => $item['product']
                ]);



                $variant = Variant::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'name' => $item['variant']
                    ],
                    [
                        'unit' => $item['unit'],
                        'minimum_stock' => 1
                    ]
                );



                if (!empty($item['aliases'])) {

                    foreach ($item['aliases'] as $alias) {

                        \App\Models\ProductAlias::updateOrCreate(
                            [
                                'variant_id' => $variant->id,
                                'alias' => strtolower(trim($alias)),
                            ],
                            [
                                'priority' => 10,
                            ]
                        );
                    }
                }
            }
        }
    }
}
