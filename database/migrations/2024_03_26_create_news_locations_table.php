<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_item_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('place_id')->nullable();
            $table->point('location')->nullable();
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedInteger('state_id')->nullable();

            $table->timestamps();
        
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_locations');
    }
};