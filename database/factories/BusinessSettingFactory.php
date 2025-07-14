<?php

namespace Database\Factories;

use App\Models\BusinessSetting;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessSettingFactory extends Factory
{
    protected $model = BusinessSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $business = Business::inRandomOrder()->first() ?? Business::factory()->create();
        
        return [
            'type' => $this->faker->randomElement(['notification', 'display', 'security', 'integration']),
            'key' => $this->faker->unique()->word,
            'value' => $this->faker->randomElement(['true', 'false', '10', '20', '30', 'en', 'np']),
            'business_id' => $business->id,
        ];
    }

    /**
     * Create notification settings
     */
    public function notification()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'notification',
                'key' => $this->faker->randomElement(['max_notifications_per_day', 'max_notifications_per_month', 'email_notifications', 'push_notifications']),
                'value' => $this->faker->randomElement(['true', 'false', '10', '20', '30', '50']),
            ];
        });
    }

    /**
     * Create display settings
     */
    public function display()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'display',
                'key' => $this->faker->randomElement(['language', 'timezone', 'date_format', 'currency']),
                'value' => $this->faker->randomElement(['en', 'np', 'UTC', 'Asia/Kathmandu', 'Y-m-d', 'USD', 'NPR']),
            ];
        });
    }

    /**
     * Create security settings
     */
    public function security()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'security',
                'key' => $this->faker->randomElement(['two_factor_auth', 'session_timeout', 'password_policy']),
                'value' => $this->faker->randomElement(['true', 'false', '30', '60', 'strong']),
            ];
        });
    }

    /**
     * Create integration settings
     */
    public function integration()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'integration',
                'key' => $this->faker->randomElement(['google_analytics', 'facebook_pixel', 'stripe_enabled']),
                'value' => $this->faker->randomElement(['true', 'false', 'UA-123456789-1', 'fbq-123456789']),
            ];
        });
    }
} 