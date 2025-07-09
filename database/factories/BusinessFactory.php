<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    protected $model = Business::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = BusinessType::inRandomOrder()->first() ?? BusinessType::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        return [
            'name' => $this->faker->company,
            'description' => $this->faker->paragraph,
            'type_id' => $type->id,
            'created_by' => $user->id,
            'contact_person_id' => $user->id,
            'is_verified' => $this->faker->boolean(20),
            'is_featured' => $this->faker->boolean(10),
            'is_active' => $this->faker->boolean(90),
            'custom_email_message' => $this->faker->optional()->sentence,
            'established_year' => $this->faker->year,
            'email' => $this->faker->unique()->companyEmail,
            'phone_1' => $this->faker->phoneNumber,
            'phone_2' => $this->faker->optional()->phoneNumber,
            'website' => $this->faker->optional()->url,
            'logo' => $this->faker->optional()->imageUrl(200, 200, 'business'),
            'cover_image' => $this->faker->optional()->imageUrl(600, 200, 'business'),
        ];
    }
}
