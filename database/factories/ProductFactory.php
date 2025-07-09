<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $business = Business::inRandomOrder()->first() ?? Business::factory()->create();
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'NPR']),
            'image' => $this->faker->imageUrl(400, 400, 'products'),
            'business_id' => $business->id,
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
