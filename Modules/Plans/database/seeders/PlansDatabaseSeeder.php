<?php

namespace Modules\Plans\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Plans\Models\Plan;

class PlansDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => '1 Day Pass',
                'price' => 49,
                'duration_days' => 1,
                'contact_limit' => 5,
            ],
            [
                'name' => '7 Day Pass',
                'price' => 99,
                'duration_days' => 7,
                'contact_limit' => 25,
            ],
            [
                'name' => '30 Day Pass',
                'price' => 199,
                'duration_days' => 30,
                'contact_limit' => null,
            ],
            [
                'name' => 'Business Plan',
                'price' => 499,
                'duration_days' => 90,
                'contact_limit' => null,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::query()->updateOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
