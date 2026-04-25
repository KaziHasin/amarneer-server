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
                'features' => ['Unlock up to 5 contacts', 'Basic search', 'Email support']
            ],
            [
                'name' => '7 Day Pass',
                'price' => 99,
                'duration_days' => 7,
                'contact_limit' => 25,
                'features' => ['Unlock up to 25 contacts', 'Advanced filters', 'Priority support', 'Save favorites']
            ],
            [
                'name' => '30 Day Pass',
                'price' => 199,
                'duration_days' => 30,
                'contact_limit' => null,
                'features' => ['Unlimited contact unlocks', 'All filters & features', '24/7 support', 'Save favorites', 'Early access to listings']
            ],
            [
                'name' => 'Business Plan',
                'price' => 499,
                'duration_days' => 90,
                'contact_limit' => null,
                'features' => ['Unlimited contact unlocks', 'All filters & features', '24/7 priority support', 'Save favorites', 'Early access to listings', 'Dedicated account manager', 'Bulk export contacts']
            ],
        ];

        foreach ($plans as $planData) {
            $features = $planData['features'];
            unset($planData['features']);

            $plan = Plan::query()->updateOrCreate(['name' => $planData['name']], $planData);

            $plan->features()->delete();
            foreach ($features as $feature) {
                $plan->features()->create(['name' => $feature]);
            }
        }
    }
}
