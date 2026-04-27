<?php

namespace Modules\Properties\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Properties\Models\Category;
class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Residential',
                'children' => [
                    'House',
                    'Flat',
                    'Apartment',
                ],
            ],
            [
                'name' => 'Commercial',
                'children' => [
                    'Shop',
                    'Office',
                    'Showroom',
                ],
            ],
        ];

        foreach ($categories as $category) {
            $parent = Category::updateOrCreate(
                ['name' => $category['name'], 'parent_id' => null],
                []
            );

            foreach ($category['children'] as $childName) {
                Category::updateOrCreate(
                    ['name' => $childName, 'parent_id' => $parent->id],
                    []
                );
            }
        }
    }
}
