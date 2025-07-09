<?php

namespace Database\Factories;

use App\Models\NewsItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsItemFactory extends Factory
{
    protected $model = NewsItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'url' => $this->faker->optional()->url,
            'image' => $this->faker->optional()->imageUrl(400, 200, 'news'),
            'published_at' => $this->faker->dateTimeThisYear,
            'original_id' => $this->faker->uuid,
            'is_active' => $this->faker->boolean(90),
            'is_rejected' => $this->faker->boolean(10),
            'is_featured' => $this->faker->boolean(10),
            'sourceable_id' => $user->id,
            'sourceable_type' => User::class,
            'created_by' => $user->id,
            'language' => $this->faker->randomElement(['en', 'np']),
        ];
    }
}
