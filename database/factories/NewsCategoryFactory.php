<?php

namespace Database\Factories;

use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsCategoryFactory extends Factory
{
    protected $model = NewsCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->unique()->word;
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'type' => $this->faker->randomElement(['geography', 'category', 'tags', 'source']),
        ];
    }
}
