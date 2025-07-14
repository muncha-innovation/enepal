<?php

namespace Database\Factories;

use App\Models\Thread;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadFactory extends Factory
{
    protected $model = Thread::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $conversation = Conversation::inRandomOrder()->first() ?? Conversation::factory()->create();
        
        return [
            'title' => $this->faker->sentence,
            'conversation_id' => $conversation->id,
            'status' => $this->faker->randomElement(['active', 'closed', 'pending']),
            'description' => $this->faker->optional()->paragraph,
            'last_message_at' => $this->faker->dateTimeThisMonth,
        ];
    }

    /**
     * Create an active thread
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }

    /**
     * Create a closed thread
     */
    public function closed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'closed',
            ];
        });
    }

    /**
     * Create a pending thread
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    /**
     * Create a thread for a specific conversation
     */
    public function forConversation($conversationId)
    {
        return $this->state(function (array $attributes) use ($conversationId) {
            return [
                'conversation_id' => $conversationId,
            ];
        });
    }
} 