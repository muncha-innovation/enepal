<?php

namespace Database\Factories;

use App\Models\AgeGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgeGroupFactory extends Factory
{
    protected $model = AgeGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $ageRanges = [
            ['name' => 'Teenagers', 'min_age' => 13, 'max_age' => 19],
            ['name' => 'Young Adults', 'min_age' => 20, 'max_age' => 29],
            ['name' => 'Adults', 'min_age' => 30, 'max_age' => 39],
            ['name' => 'Middle-aged', 'min_age' => 40, 'max_age' => 49],
            ['name' => 'Senior Adults', 'min_age' => 50, 'max_age' => 59],
            ['name' => 'Seniors', 'min_age' => 60, 'max_age' => 69],
            ['name' => 'Elderly', 'min_age' => 70, 'max_age' => 100],
        ];

        $ageRange = $this->faker->randomElement($ageRanges);
        
        return [
            'name' => $ageRange['name'],
            'description' => $this->faker->sentence,
            'min_age' => $ageRange['min_age'],
            'max_age' => $ageRange['max_age'],
        ];
    }

    /**
     * Create teenagers age group
     */
    public function teenagers()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Teenagers',
                'min_age' => 13,
                'max_age' => 19,
            ];
        });
    }

    /**
     * Create young adults age group
     */
    public function youngAdults()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Young Adults',
                'min_age' => 20,
                'max_age' => 29,
            ];
        });
    }

    /**
     * Create adults age group
     */
    public function adults()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Adults',
                'min_age' => 30,
                'max_age' => 39,
            ];
        });
    }

    /**
     * Create middle-aged age group
     */
    public function middleAged()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Middle-aged',
                'min_age' => 40,
                'max_age' => 49,
            ];
        });
    }

    /**
     * Create seniors age group
     */
    public function seniors()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Seniors',
                'min_age' => 60,
                'max_age' => 69,
            ];
        });
    }
}
