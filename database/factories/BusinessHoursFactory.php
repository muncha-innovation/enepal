<?php

namespace Database\Factories;

use App\Models\BusinessHours;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessHoursFactory extends Factory
{
    protected $model = BusinessHours::class;

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
            'day' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
            'open_time' => $this->faker->time('H:i'),
            'close_time' => $this->faker->time('H:i'),
            'is_open' => $this->faker->boolean(80), // 80% chance of being open
        ];
    }

    /**
     * Create business hours for a specific day
     */
    public function forDay($day)
    {
        return $this->state(function (array $attributes) use ($day) {
            return [
                'day' => $day,
            ];
        });
    }

    /**
     * Create business hours for weekdays (Monday-Friday)
     */
    public function weekdays()
    {
        return $this->state(function (array $attributes) {
            return [
                'day' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
                'is_open' => true,
            ];
        });
    }

    /**
     * Create business hours for weekends (Saturday-Sunday)
     */
    public function weekends()
    {
        return $this->state(function (array $attributes) {
            return [
                'day' => $this->faker->randomElement(['Saturday', 'Sunday']),
                'is_open' => $this->faker->boolean(60), // 60% chance of being open on weekends
            ];
        });
    }

    /**
     * Create closed business hours
     */
    public function closed()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_open' => false,
                'open_time' => null,
                'close_time' => null,
            ];
        });
    }

    /**
     * Create open business hours
     */
    public function open()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_open' => true,
            ];
        });
    }
} 