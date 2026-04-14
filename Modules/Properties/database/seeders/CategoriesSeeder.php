<?php

namespace Modules\Properties\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Properties\Models\Category;
class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // Parent Categories
        $residential = Category::create([
            'name' => 'Residential',
            'parent_id' => null,
        ]);

        $commercial = Category::create([
            'name' => 'Commercial',
            'parent_id' => null,
        ]);

        Category::create([
            'name' => 'House',
            'parent_id' => $residential->id,
        ]);

        Category::create([
            'name' => 'Flat',
            'parent_id' => $residential->id,
        ]);

        Category::create([
            'name' => 'Apartment',
            'parent_id' => $residential->id,
        ]);

        Category::create([
            'name' => 'Shop',
            'parent_id' => $commercial->id,
        ]);

        Category::create([
            'name' => 'Office',
            'parent_id' => $commercial->id,
        ]);

        Category::create([
            'name' => 'Showroom',
            'parent_id' => $commercial->id,
        ]);
    }
}
