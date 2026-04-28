<?php

namespace Modules\Properties\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Properties\Models\Amenity;
use Modules\Properties\Models\Category;

class AmenitiesSeeder extends Seeder
{
    public function run(): void
    {
        $residentialCategory = Category::where('name', 'Residential')->first();
        $commercialCategory = Category::where('name', 'Commercial')->first();

        $amenities = [
            // Residential Amenities
            ['name' => 'Parking', 'icon' => 'fas fa-parking', 'category_id' => $residentialCategory?->id],
            ['name' => 'Lift', 'icon' => 'fas fa-elevator', 'category_id' => $residentialCategory?->id],
            ['name' => 'Security', 'icon' => 'fas fa-shield-alt', 'category_id' => $residentialCategory?->id],
            ['name' => 'Power Backup', 'icon' => 'fas fa-bolt', 'category_id' => $residentialCategory?->id],
            ['name' => 'Gym', 'icon' => 'fas fa-dumbbell', 'category_id' => $residentialCategory?->id],
            ['name' => 'Swimming Pool', 'icon' => 'fas fa-swimmer', 'category_id' => $residentialCategory?->id],
            ['name' => 'Garden', 'icon' => 'fas fa-tree', 'category_id' => $residentialCategory?->id],
            ['name' => 'Balcony', 'icon' => 'fas fa-campground', 'category_id' => $residentialCategory?->id],

            // Commercial Amenities
            ['name' => 'Visitor Parking', 'icon' => 'fas fa-parking', 'category_id' => $commercialCategory?->id],
            ['name' => 'Cafeteria', 'icon' => 'fas fa-coffee', 'category_id' => $commercialCategory?->id],
            ['name' => 'Conference Room', 'icon' => 'fas fa-users', 'category_id' => $commercialCategory?->id],
            ['name' => 'Central AC', 'icon' => 'fas fa-snowflake', 'category_id' => $commercialCategory?->id],
            ['name' => 'CCTV Security', 'icon' => 'fas fa-video', 'category_id' => $commercialCategory?->id],
            ['name' => 'Freight Elevator', 'icon' => 'fas fa-truck-loading', 'category_id' => $commercialCategory?->id],
            ['name' => 'Fire Safety', 'icon' => 'fas fa-fire-extinguisher', 'category_id' => $commercialCategory?->id],
            ['name' => 'Wheelchair Accessible', 'icon' => 'fas fa-wheelchair', 'category_id' => $commercialCategory?->id],
        ];

        // We can truncate to prevent duplicates with wrong category_id if we change things, but updateOrCreate is fine
        foreach ($amenities as $amenity) {
            if ($amenity['category_id']) {
                Amenity::updateOrCreate(
                    ['name' => $amenity['name']],
                    ['icon' => $amenity['icon'], 'category_id' => $amenity['category_id']]
                );
            }
        }
    }
}