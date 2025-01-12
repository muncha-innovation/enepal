<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->enum('geography', 'category', 'tags', 'source')->default('category');
            $table->nestedSet();
            $table->timestamps();
        });

        Schema::create('news_item_category', function (Blueprint $table) {
            $table->foreignId('news_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('news_category_id')->constrained()->onDelete('cascade');
            $table->primary(['news_item_id', 'news_category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_item_category');
        Schema::dropIfExists('news_categories');
    }
}; 