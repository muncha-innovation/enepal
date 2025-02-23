<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Grimzy\LaravelMysqlSpatial\Types\Point as Point;
class CountriesTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {        
        if(config('app.env') == 'local'){
            // Disable foreign key checks before truncate
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Country::truncate();
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->seedNepal();
        } else {
            // Disable foreign key checks before truncate
            \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Country::truncate();
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->seedAll();   
        }
        
    }

    private function seedAll()
    {
        $JSON_countries = Country::allJSON();
        foreach ($JSON_countries as $country) {
            // dd($country['latitude']);
            // dd(new Point((float)$country['latitude'], (float)$country['longitude']));
            $c = Country::firstOrCreate([
                'name'             => (isset($country['name']) ? $country['name'] : null),
                'dial_code'        => (isset($country['phonecode']) ? $country['phonecode'] : null),
                'code'             => (isset($country['iso2']) ? $country['iso2'] : null),
                'flag'             => (isset($country['iso2']) ? $country['iso2'].'.png' : null),
                'currency_name'    => (isset($country['currency_name']) ? $country['currency_name'] : null),
                'currency_code'    => (isset($country['currency']) ? $country['currency'] : null),
                'currency_symbol'  => (isset($country['currency_symbol']) ? $country['currency_symbol'] : null),
                'dial_min_length'  => (isset($country['minLength']) ? $country['minLength'] : 6),
                'dial_max_length'  => (isset($country['maxLength']) ? $country['maxLength'] : 13),
                'location'         => new Point((float)$country['latitude'], (float)$country['longitude']),
            ]);
            // assign states and cities
            if(isset($country['states'])){
                foreach ($country['states'] as $state) {
                    $c->states()->firstOrCreate([
                        'name'     => (isset($state['name']) ? $state['name'] : null),
                        'code'     => (isset($state['state_code']) ? $state['state_code'] : null),
                        'location' => new Point((float)$state['latitude'], (float)$state['longitude']),
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
                ['name' => 'Koshi', 'state_code' => 'KO'],
                ['name' => 'Sagarmatha', 'state_code' => 'SA'],
                ['name' => 'Sudurpashchim', 'state_code' => 'SU'],
            ]
        ];
        $c = Country::firstOrCreate([
            'name'            => 'Nepal',
            'dial_code'       => '+977',
            'code'            => 'NP',
            'flag'            => 'NP.png',
            'currency_name'   => 'NPR',
            'currency_code'   => 'RS',
            'currency_symbol' => 'NPR',
            'dial_min_length' => 9,
            'dial_max_length' => 10,
        ]);
        if(isset($country['states'])){
            foreach ($country['states'] as $state) {
                $c->states()->firstOrCreate([
                    'name' => (isset($state['name']) ? $state['name'] : null),
                    'code' => (isset($state['state_code']) ? $state['state_code'] : null),
                ]);
            }
        }
    }
}
