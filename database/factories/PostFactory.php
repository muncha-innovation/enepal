<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $business = Business::inRandomOrder()->first() ?? Business::factory()->create();
        $title = $this->faker->sentence;
        return [
            'title' => [
                'en' => $title,
                'np' => $this->faker->sentence
            ],
            'short_description' => [
                'en' => $this->faker->sentence,
                'np' => $this->faker->sentence
            ],
            'content' => [
                'en' => $this->faker->paragraphs(3, true),
                'np' => $this->faker->paragraphs(3, true)
            ],
            'image' => $this->faker->imageUrl(800, 400, 'posts'),
            'user_id' => $user->id,
            'business_id' => $business->id,
            'is_active' => $this->faker->boolean(90),
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
        ];
    }
}
