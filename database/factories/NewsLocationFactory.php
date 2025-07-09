<?php

namespace Database\Factories;

use App\Models\NewsLocation;
use App\Models\NewsItem;
use App\Models\Country;
use App\Models\State;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsLocationFactory extends Factory
{
    protected $model = NewsLocation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $newsItem = NewsItem::inRandomOrder()->first() ?? NewsItem::factory()->create();
        $country = Country::inRandomOrder()->first() ?? Country::factory()->create();
        $state = State::where('country_id', $country->id)->inRandomOrder()->first() ?? State::factory()->create(['country_id' => $country->id]);
        $lat = $this->faker->latitude;
        $lng = $this->faker->longitude;
        return [
            'news_item_id' => $newsItem->id,
            'name' => $this->faker->city,
            'place_id' => $this->faker->optional()->uuid,
            'location' => new Point($lat, $lng),
            'country_id' => $country->id,
            'state_id' => $state->id,
        ];
    }
}
