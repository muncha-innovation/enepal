<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Seeder;

class BusinessTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $types = BusinessType::all();
        if(sizeof($types)==0){
            $types = [
                ['title' => 'Restaurant', 'slug' => 'restaurant', 'icon' => asset('images/business/restaurant.png')],
                ['title' => 'Shop', 'slug' => 'shop', 'icon' => asset('images/business/shop.jpg')],

                ['title' => 'Travel Agencies', 'slug' => 'travel-agency', 'icon' => asset('images/business/travel-agency.png')],

                ['title' => 'Association', 'slug' => 'association', 'icon' => asset('images/business/association.png')],

                ['title' => 'Manpower Agencies', 'slug' => 'manpower', 'icon' => asset('images/business/manpower.jpg')],

                ['title' => 'Educational Consultancies', 'slug' => 'consultancy', 'icon' => asset('images/business/education-consultancy.jpg')],

                ['title' => 'News Outlets', 'slug' => 'news-outlets', 'icon' => asset('images/business/news-outlets.png')],
            ];
            foreach ($types as $type) {
                $type = BusinessType::firstOrCreate(
                    $type
                );
            }   
        }
    }
}
