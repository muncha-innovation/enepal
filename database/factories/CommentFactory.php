<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

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
            'post_id' => $post->id,
            'user_id' => $user->id,
            'comment' => $this->faker->paragraph,
            'parent_id' => null, // Top-level comment by default
            'is_approved' => $this->faker->boolean(80), // 80% chance of being approved
        ];
    }

    /**
     * Create a reply to an existing comment
     */
    public function reply()
    {
        return $this->state(function (array $attributes) {
            $parentComment = Comment::inRandomOrder()->first() ?? Comment::factory()->create();
            
            return [
                'parent_id' => $parentComment->id,
                'post_id' => $parentComment->post_id,
            ];
        });
    }

    /**
     * Create an approved comment
     */
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_approved' => true,
            ];
        });
    }

    /**
     * Create a pending comment
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_approved' => false,
            ];
        });
    }
} 