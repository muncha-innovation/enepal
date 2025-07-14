<?php

namespace Database\Factories;

use App\Models\SocialNetwork;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialNetworkFactory extends Factory
{
    protected $model = SocialNetwork::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'icon' => $this->faker->randomElement(['📱', '💬', '📷', '🎥', '🔗', '📧', '📞', '🌐']),
        ];
    }

    /**
     * Create Facebook social network
     */
    public function facebook()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Facebook',
                'icon' => '📘',
            ];
        });
    }

    /**
     * Create Instagram social network
     */
    public function instagram()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Instagram',
                'icon' => '📷',
            ];
        });
    }

    /**
     * Create Twitter social network
     */
    public function twitter()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Twitter',
                'icon' => '🐦',
            ];
        });
    }

    /**
     * Create LinkedIn social network
     */
    public function linkedin()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'LinkedIn',
                'icon' => '💼',
            ];
        });
    }

    /**
     * Create YouTube social network
     */
    public function youtube()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'YouTube',
                'icon' => '📺',
            ];
        });
    }

    /**
     * Create WhatsApp social network
     */
    public function whatsapp()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'WhatsApp',
                'icon' => '💬',
            ];
        });
    }
} 