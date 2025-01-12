<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class BusinessFacilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $businessFacilities = [
            ['title' => 'Parking', 'input_type' => 'radio', 'business_types'=>[1,2,3,4,5]],
            ['title' => 'Credit Cards', 'input_type' =>'radio', 'business_types' => [1,2,3]],
            ['title' => 'Online Orders', 'input_type' => 'radio', 'business_types' => [1,2,3]],
            ['title' => 'Wi-Fi', 'input_type' => 'radio', 'business_types' => [1]],
            ['title' => 'Opening Hours', 'input_type' => 'text', 'business_types' => [1,2,3,5,6]],
            ['title' => 'Home Page', 'input_type' => 'text', 'business_types' => [1,2,3,4,5,6,7]],
            ['title' => 'Instagram', 'input_type' => 'text', 'business_types' => [1,2,3,4,5,6,7]],
            ['title' => 'Facebook', 'input_type' => 'text', 'business_types' => [1,2,3,4,5,6,7]]
        ];
        foreach ($businessFacilities as $facility) {
            Facility::create($facility);
        }
    }
}
