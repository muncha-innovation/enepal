<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateLocationColumnInAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("UPDATE addresses SET location = ST_GeomFromText('POINT(0 0)') WHERE location IS NULL");
        
        // Change location column to NOT NULL
        DB::statement("ALTER TABLE addresses MODIFY location POINT NOT NULL");
        
        // Check if the index exists before trying to drop it
        try {
            DB::statement("ALTER TABLE addresses DROP INDEX addresses_location_spatial");
        } catch (\Exception $e) {
        }
        
        
        
        // Now add the spatial index
        DB::statement("ALTER TABLE addresses ADD SPATIAL INDEX addresses_location_spatial(location)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the spatial index
        try {
            DB::statement("ALTER TABLE addresses DROP INDEX addresses_location_spatial");
        } catch (\Exception $e) {
            // Index doesn't exist, continue with migration
        }
        
        
        
        // Change location back to nullable
        DB::statement("ALTER TABLE addresses MODIFY location POINT NULL");
    }
}
