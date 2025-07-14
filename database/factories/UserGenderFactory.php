<?php

namespace Database\Factories;

use App\Models\UserGender;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserGenderFactory extends Factory
{
    protected $model = UserGender::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $genders = [
            ['name' => 'Male', 'description' => 'Male gender'],
            ['name' => 'Female', 'description' => 'Female gender'],
            ['name' => 'Other', 'description' => 'Other gender identity'],
            ['name' => 'Prefer not to say', 'description' => 'Prefer not to disclose gender'],
        ];

        $gender = $this->faker->randomElement($genders);
        
        return [
            'name' => $gender['name'],
            'description' => $gender['description'],
        ];
    }

    /**
     * Create male gender
     */
    public function male()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Male',
                'description' => 'Male gender',
            ];
        });
    }

    /**
     * Create female gender
     */
    public function female()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Female',
                'description' => 'Female gender',
            ];
        });
    }

    /**
     * Create other gender
     */
    public function other()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Other',
                'description' => 'Other gender identity',
            ];
        });
    }

    /**
     * Create prefer not to say gender
     */
    public function preferNotToSay()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Prefer not to say',
                'description' => 'Prefer not to disclose gender',
            ];
        });
    }
}
