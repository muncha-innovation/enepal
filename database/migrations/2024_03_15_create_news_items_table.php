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
            $table->foreignId('source_id')->constrained('news_sources')->nullable();
            $table->string('original_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url');
            $table->string('image')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('language', 4)->default('np');
            $table->timestamp('published_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_items');
    }
}; 