<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Vendor;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $vendor = Vendor::inRandomOrder()->first() ?? Vendor::factory()->create();
        $business = Business::inRandomOrder()->first() ?? Business::factory()->create();
        return [
            'vendor_id' => $vendor->id,
            'business_id' => $business->id,
            'conversation_id' => $this->faker->unique()->uuid,
            'title' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['active', 'closed', 'pending']),
            'last_message_at' => $this->faker->dateTimeThisMonth,
            'created_at' => $this->faker->dateTimeThisYear,
            'updated_at' => $this->faker->dateTimeThisMonth,
        ];
    }
}
