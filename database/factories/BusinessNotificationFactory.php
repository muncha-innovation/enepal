<?php

namespace Database\Factories;

use App\Models\BusinessNotification;
use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessNotificationFactory extends Factory
{
    protected $model = BusinessNotification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $business = Business::inRandomOrder()->first() ?? Business::factory()->create();
        $verifier = User::inRandomOrder()->first() ?? User::factory()->create();
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'image' => $this->faker->optional()->imageUrl(400, 200, 'business'),
            'business_id' => $business->id,
            'is_active' => $this->faker->boolean(90),
            'is_private' => $this->faker->boolean(10),
            'is_verified' => $this->faker->boolean(20),
            'verified_by' => $verifier->id,
            'is_sent' => $this->faker->boolean(50),
            'sent_at' => $this->faker->optional()->dateTimeThisYear,
        ];
    }
}
