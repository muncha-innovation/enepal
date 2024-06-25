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
        $this->seedAll();   
        
    }

    private function seedAll()
 {
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

 }
    private function seedNepal() {
        $country = [
            'states' => [
                ['name' => 'Bagmati', 'state_code' => 'BA'],
                ['name' => 'Gandaki', 'state_code' => 'GA'],
                ['name' => 'Karnali', 'state_code' => 'KA'],
                ['name' => 'Lumbini', 'state_code' => 'LU'],
                ['name' => 'Province No. 1', 'state_code' => '1'],
                ['name' => 'Province No. 2', 'state_code' => '2'],
                ['name' => 'Sudurpashchim', 'state_code' => 'SU'],
                ['name' => 'State No. 3', 'state_code' => '3'],
                ['name' => 'State No. 4', 'state_code' => '4'],
                ['name' => 'State No. 5', 'state_code' => '5'],
            ]        ];
        $c = Country::firstOrCreate([
            'name'           => 'Nepal',
            'dial_code'              => '+977',
            'code'   => 'NP',
            'flag'   => 'NP.png',
            'currency_name'   => 'NPR',
            'currency_code'   => 'RS',
            'currency_symbol'   => 'NPR',
            'dial_min_length'   => 9,
            'dial_max_length'   => 10,
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
}
