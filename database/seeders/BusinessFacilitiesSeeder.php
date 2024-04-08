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
        $businessFacilities = ['Parking', 'Home Delivery', 'Takeaway'];
        foreach ($businessFacilities as $businessFacilities) {
            Facility::create(['title' => $businessFacilities]);
        }
    }
}
