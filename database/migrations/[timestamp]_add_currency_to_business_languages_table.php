<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('business_language', function (Blueprint $table) {
            $table->string('currency', 3)->default('USD')->after('price');
        });
    }

    public function down()
    {
        Schema::table('business_language', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
