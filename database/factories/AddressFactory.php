<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $country = Country::inRandomOrder()->first() ?? Country::factory()->create();
        $state = State::where('country_id', $country->id)->inRandomOrder()->first() ?? State::factory()->create(['country_id' => $country->id]);
        $lat = $this->faker->latitude;
        $lng = $this->faker->longitude;
        return [
            'country_id' => $country->id,
            'state_id' => $state->id,
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->optional()->secondaryAddress,
            'postal_code' => $this->faker->postcode,
            'city' => $this->faker->city,
            'prefecture' => $this->faker->optional()->citySuffix,
            'town' => $this->faker->optional()->city,
            'street' => $this->faker->streetName,
            'building' => $this->faker->optional()->buildingNumber,
            'location' => new Point($lat, $lng),
            'address_type' => $this->faker->randomElement(['primary', 'secondary', 'birth', 'current', 'branch', 'other']),
        ];
    }
}
