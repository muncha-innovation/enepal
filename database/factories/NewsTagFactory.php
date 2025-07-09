<?php

namespace Database\Factories;

use App\Models\NewsTag;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsTagFactory extends Factory
{
    protected $model = NewsTag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'usage_count' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
