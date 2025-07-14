<?php

namespace Database\Factories;

use App\Models\BusinessType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BusinessTypeFactory extends Factory
{
    protected $model = BusinessType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->unique()->company;
        
        return [
            'title' => $title,
            'description' => $this->faker->paragraph,
            'icon' => $this->faker->randomElement(['🏢', '🏪', '🏭', '🏨', '🏥', '🏫', '🏦', '🚗', '✈️', '🛳️']),
            'slug' => Str::slug($title),
        ];
    }

    /**
     * Create a business type for education consultancy
     */
    public function educationConsultancy()
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => 'Education Consultancy',
                'description' => 'Services for international education and study abroad',
                'icon' => '🎓',
                'slug' => 'education-consultancy',
            ];
        });
    }

    /**
     * Create a business type for manpower agency
     */
    public function manpowerAgency()
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => 'Manpower Agency',
                'description' => 'Recruitment and placement services for overseas employment',
                'icon' => '👥',
                'slug' => 'manpower-agency',
            ];
        });
    }

    /**
     * Create a business type for travel agency
     */
    public function travelAgency()
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => 'Travel Agency',
                'description' => 'Travel and tourism services',
                'icon' => '✈️',
                'slug' => 'travel-agency',
            ];
        });
    }
} 