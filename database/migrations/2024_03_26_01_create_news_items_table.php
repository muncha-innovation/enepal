<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news_items', function (Blueprint $table) {
            $table->id();
            $table->morphs('sourceable');
            $table->text('original_id')->nullable();
            $table->text('title');
            $table->text('description')->nullable();
            $table->text('url')->nullable();
            $table->text('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('language', 4)->default('np');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_items');
    }
}; 