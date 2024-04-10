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
                'Restaurant',
                'Shop',
                'Travel Agency',
                'Association'
            ];
            foreach ($types as $type) {
                BusinessType::firstOrCreate([
                    'title'           => $type,
                ]);
            }   
        }
    }
}
