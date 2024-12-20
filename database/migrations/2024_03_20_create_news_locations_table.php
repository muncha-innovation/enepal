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
            $table->string('name');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('radius', 8, 2)->default(0); // radius in kilometers
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_locations');
    }
}; 