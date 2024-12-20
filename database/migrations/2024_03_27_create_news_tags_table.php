<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });

        Schema::create('news_item_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_item_id')->constrained('news_items')->onDelete('cascade');
            $table->foreignId('news_tag_id')->constrained('news_tags')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_item_tag');
        Schema::dropIfExists('news_tags');
    }
}; 