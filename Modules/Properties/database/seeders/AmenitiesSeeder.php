<?php

namespace Modules\Properties\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Properties\Models\Amenity;

class AmenitiesSeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'Parking', 'icon' => 'fas fa-parking'],
            ['name' => 'Lift', 'icon' => 'fas fa-elevator'],
            ['name' => 'Security', 'icon' => 'fas fa-shield-alt'],
            ['name' => 'Power Backup', 'icon' => 'fas fa-bolt'],
            ['name' => 'Gym', 'icon' => 'fas fa-dumbbell'],
            ['name' => 'Swimming Pool', 'icon' => 'fas fa-swimmer'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::updateOrCreate(
                ['name' => $amenity['name']],
                ['icon' => $amenity['icon']]
            );
        }
    }
}