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
            ['name' => 'Parking', 'input_type' => 'radio', 'business_types'=>[1,2,3,4,5]],
            ['name' => 'Credit Cards', 'input_type' =>'radio', 'business_types' => [1,2,3]],
            ['name' => 'Online Orders', 'input_type' => 'radio', 'business_types' => [1,2,3]],
            ['name' => 'Wi-Fi', 'input_type' => 'radio', 'business_types' => [1]],
            ['name' => 'Opening Hours', 'input_type' => 'text', 'business_types' => [1,2,3,5,6]],
            ['name' => 'Home Page', 'input_type' => 'text', 'business_types' => [1,2,3,4,5,6,7]],
            ['name' => 'Instagram', 'input_type' => 'text', 'business_types' => [1,2,3,4,5,6,7]],
            ['name' => 'Facebook', 'input_type' => 'text', 'business_types' => [1,2,3,4,5,6,7]]
        ];
        foreach ($businessFacilities as $facility) {
            $businessTypes = $facility['business_types'];
            unset($facility['business_types']);
            $f = Facility::create($facility);
            $f->businessTypes()->attach($businessTypes);
        }
    }
}
