<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\Thread;
use App\Models\User;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $conversation = Conversation::inRandomOrder()->first() ?? Conversation::factory()->create();
        $thread = Thread::inRandomOrder()->first() ?? Thread::factory()->create(['conversation_id' => $conversation->id]);
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        
        return [
            'conversation_id' => $conversation->id,
            'thread_id' => $thread->id,
            'sender_id' => $user->id,
            'sender_type' => User::class,
            'content' => $this->faker->paragraph,
            'attachments' => $this->faker->optional()->randomElements([
                ['name' => 'document.pdf', 'path' => 'attachments/document.pdf', 'size' => 1024000],
                ['name' => 'image.jpg', 'path' => 'attachments/image.jpg', 'size' => 512000],
                ['name' => 'video.mp4', 'path' => 'attachments/video.mp4', 'size' => 2048000],
            ], $this->faker->numberBetween(0, 2)),
            'is_read' => $this->faker->boolean(70), // 70% chance of being read
            'opened_at' => $this->faker->optional()->dateTimeThisMonth,
        ];
    }

    /**
     * Create a message from a user
     */
    public function fromUser($userId = null)
    {
        return $this->state(function (array $attributes) use ($userId) {
            $user = $userId ? User::find($userId) : (User::inRandomOrder()->first() ?? User::factory()->create());
            
            return [
                'sender_id' => $user->id,
                'sender_type' => User::class,
            ];
        });
    }

    /**
     * Create a message from a business
     */
    public function fromBusiness($businessId = null)
    {
        return $this->state(function (array $attributes) use ($businessId) {
            $business = $businessId ? Business::find($businessId) : (Business::inRandomOrder()->first() ?? Business::factory()->create());
            
            return [
                'sender_id' => $business->id,
                'sender_type' => Business::class,
            ];
        });
    }

    /**
     * Create a read message
     */
    public function read()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_read' => true,
                'opened_at' => $this->faker->dateTimeThisMonth,
            ];
        });
    }

    /**
     * Create an unread message
     */
    public function unread()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_read' => false,
                'opened_at' => null,
            ];
        });
    }

    /**
     * Create a message with attachments
     */
    public function withAttachments()
    {
        return $this->state(function (array $attributes) {
            return [
                'attachments' => $this->faker->randomElements([
                    ['name' => 'document.pdf', 'path' => 'attachments/document.pdf', 'size' => 1024000],
                    ['name' => 'image.jpg', 'path' => 'attachments/image.jpg', 'size' => 512000],
                    ['name' => 'video.mp4', 'path' => 'attachments/video.mp4', 'size' => 2048000],
                ], $this->faker->numberBetween(1, 3)),
            ];
        });
    }

    /**
     * Create a message for a specific conversation
     */
    public function forConversation($conversationId)
    {
        return $this->state(function (array $attributes) use ($conversationId) {
            return [
                'conversation_id' => $conversationId,
            ];
        });
    }

    /**
     * Create a message for a specific thread
     */
    public function forThread($threadId)
    {
        return $this->state(function (array $attributes) use ($threadId) {
            $thread = Thread::find($threadId);
            
            return [
                'thread_id' => $threadId,
                'conversation_id' => $thread ? $thread->conversation_id : null,
            ];
        });
    }
} 