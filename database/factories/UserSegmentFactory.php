<?php

namespace Database\Factories;

use App\Models\UserSegment;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSegmentFactory extends Factory
{
    protected $model = UserSegment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $business = Business::inRandomOrder()->first() ?? Business::factory()->create();
        
        return [
            'business_id' => $business->id,
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->sentence,
            'type' => $this->faker->randomElement(['demographic', 'behavioral', 'geographic', 'custom']),
            'is_default' => $this->faker->boolean(20), // 20% chance of being default
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Create a demographic segment
     */
    public function demographic()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'demographic',
                'name' => $this->faker->randomElement(['Young Adults', 'Seniors', 'Students', 'Professionals', 'Families']),
            ];
        });
    }

    /**
     * Create a behavioral segment
     */
    public function behavioral()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'behavioral',
                'name' => $this->faker->randomElement(['Frequent Visitors', 'New Users', 'Inactive Users', 'Premium Users', 'Engaged Users']),
            ];
        });
    }

    /**
     * Create a geographic segment
     */
    public function geographic()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'geographic',
                'name' => $this->faker->randomElement(['Local Users', 'International Users', 'Urban Users', 'Rural Users']),
            ];
        });
    }

    /**
     * Create a custom segment
     */
    public function custom()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'custom',
                'name' => $this->faker->unique()->words(2, true),
            ];
        });
    }

    /**
     * Create a default segment
     */
    public function default()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_default' => true,
                'is_active' => true,
            ];
        });
    }

    /**
     * Create an active segment
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * Create an inactive segment
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
} 