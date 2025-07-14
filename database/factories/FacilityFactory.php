<?php

namespace Database\Factories;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacilityFactory extends Factory
{
    protected $model = Facility::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'input_type' => $this->faker->randomElement(['checkbox', 'radio', 'text', 'number', 'select']),
            'icon' => $this->faker->randomElement(['📱', '💻', '🚗', '🏠', '🍽️', '🏥', '🎓', '💼', '🌍', '💰']),
        ];
    }

    /**
     * Create a facility with checkbox input type
     */
    public function checkbox()
    {
        return $this->state(function (array $attributes) {
            return [
                'input_type' => 'checkbox',
            ];
        });
    }

    /**
     * Create a facility with radio input type
     */
    public function radio()
    {
        return $this->state(function (array $attributes) {
            return [
                'input_type' => 'radio',
            ];
        });
    }

    /**
     * Create a facility with text input type
     */
    public function text()
    {
        return $this->state(function (array $attributes) {
            return [
                'input_type' => 'text',
            ];
        });
    }

    /**
     * Create a facility with number input type
     */
    public function number()
    {
        return $this->state(function (array $attributes) {
            return [
                'input_type' => 'number',
            ];
        });
    }

    /**
     * Create a facility with select input type
     */
    public function select()
    {
        return $this->state(function (array $attributes) {
            return [
                'input_type' => 'select',
            ];
        });
    }
} 