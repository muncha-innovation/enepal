<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $dob = $this->faker->optional()->dateTimeBetween('-60 years', '-18 years');
        $hasPassport = $this->faker->boolean();

        return [
            'first_name' => $this->faker->firstName,
            'last_name'  => $this->faker->lastName,
            'email'      => $this->faker->unique()->safeEmail,
            'dob'        => $dob ? $dob->format('Y-m-d') : null,
            'has_passport' => $hasPassport,
            'email_verified_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'password' => Hash::make('password'), // or bcrypt('password')
            'phone' => $this->faker->optional()->phoneNumber,
            'phone_verified_at' => $this->faker->optional()->dateTimeThisYear(),
            'profile_picture' => $this->faker->optional()->imageUrl(300, 300, 'people'),
            'force_update_password' => $this->faker->boolean(10), // 10% chance
            'last_password_updated' => $this->faker->optional()->dateTimeBetween('-6 months', 'now'),
            'fcm_token' => $this->faker->optional()->sha256,
            'fcm_token_updated_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'is_active' => $this->faker->boolean(90), // 90% chance active
            'created_by' => null, // You can override this if needed later
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

