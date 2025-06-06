<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'event_limit' => 1,
                'monthly_price' => 0,
                'annual_price' => 0, // ~17% discount
                'features' => [
                    'basic_website_builder',
                    'attendee_management',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'First',
                'slug' => 'first',
                'event_limit' => 3,
                'monthly_price' => 9.99,
                'annual_price' => 99.99, // ~17% discount
                'features' => [
                    'basic_website_builder',
                    'attendee_management',
                    'basic_support'
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Second',
                'slug' => 'second',
                'event_limit' => 10,
                'monthly_price' => 29.99,
                'annual_price' => 299.99, // ~17% discount
                'features' => [
                    'advanced_website_builder',
                    'attendee_management',
                    'exhibitor_management',
                    'custom_branding',
                    'priority_support'
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'event_limit' => null, // Unlimited
                'monthly_price' => 99.99,
                'annual_price' => 999.99, // ~17% discount
                'features' => [
                    'unlimited_events',
                    'advanced_website_builder',
                    'attendee_management',
                    'exhibitor_management',
                    'custom_branding',
                    'advanced_analytics',
                    'api_access',
                    'dedicated_support',
                    'white_label'
                ],
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            \App\Models\Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
