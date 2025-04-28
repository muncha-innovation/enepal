<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateBusinessVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_vendor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('vendor_id');
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->unique(['business_id', 'vendor_id']);
        });

        // Associate all existing businesses with the default vendor
        $vendorId = DB::table('vendors')->where('name', 'Enepal Website')->value('id');
        if ($vendorId) {
            $businesses = DB::table('businesses')->pluck('id');
            foreach ($businesses as $businessId) {
                DB::table('business_vendor')->insert([
                    'business_id' => $businessId,
                    'vendor_id' => $vendorId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_vendor');
    }
}
