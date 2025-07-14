<?php

namespace Database\Factories;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    protected $model = Like::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $post = Post::inRandomOrder()->first() ?? Post::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        
        return [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ];
    }

    /**
     * Create a like for a specific post
     */
    public function forPost($postId)
    {
        return $this->state(function (array $attributes) use ($postId) {
            return [
                'post_id' => $postId,
            ];
        });
    }

    /**
     * Create a like from a specific user
     */
    public function fromUser($userId)
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
            ];
        });
    }
} 