<?php

namespace Database\Factories;

use App\Models\Gallery;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $business = Business::inRandomOrder()->first() ?? Business::factory()->create();
        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->sentence,
            'image' => $this->faker->imageUrl(600, 400, 'gallery'),
            'business_id' => $business->id,
            'is_active' => $this->faker->boolean(90),
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }
}
