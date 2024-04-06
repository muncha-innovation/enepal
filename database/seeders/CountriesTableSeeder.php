<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

         // Empty the table
        $countries = Country::all();

        // if(sizeof($countries)==0){
        // Get all from the JSON file
        $JSON_countries = Country::allJSON();
        foreach ($JSON_countries as $country) {
            $c = Country::firstOrCreate([
                'name'           => ((isset($country['name'])) ? $country['name'] : null),
                'dial_code'              => ((isset($country['phone_code'])) ? $country['phone_code'] : null),
                'code'   => ((isset($country['iso3'])) ? $country['iso3'] : null),
                'flag'   => ((isset($country['iso2'])) ? $country['iso2'].'.png' : null),
                'currency_name'   => ((isset($country['currency_name'])) ? $country['currency_name'] : null),
                'currency_code'   => ((isset($country['currency'])) ? $country['currency'] : null),
                'currency_symbol'   => ((isset($country['currency_symbol'])) ? $country['currency_symbol'] : null),
                'dial_min_length'   => ((isset($country['minLength'])) ? $country['minLength'] : 6),
                'dial_max_length'   => ((isset($country['maxLength'])) ? $country['maxLength'] : 13),
            ]);
            // assign states and cities
            if(isset($country['states'])){
                foreach ($country['states'] as $state) {
                    $c->states()->firstOrCreate([
                        'name'           => ((isset($state['name'])) ? $state['name'] : null),
                        'code'   => ((isset($state['state_code'])) ? $state['state_code'] : null),
                    ]);
                }
            }
        }
        // }
    }
}
