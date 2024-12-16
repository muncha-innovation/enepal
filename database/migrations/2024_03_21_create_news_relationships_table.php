<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_news_id')->constrained('news_items')->onDelete('cascade');
            $table->foreignId('child_news_id')->constrained('news_items')->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate relationships
            $table->unique(['parent_news_id', 'child_news_id']);
        });

        // First drop the foreign key constraint
        Schema::table('news_items', function (Blueprint $table) {
            $table->dropForeign(['main_news_id']);
        });

        // Then drop the column
        Schema::table('news_items', function (Blueprint $table) {
            $table->dropColumn('main_news_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_relationships');
        
        Schema::table('news_items', function (Blueprint $table) {
            $table->foreignId('main_news_id')->nullable()->constrained('news_items')->nullOnDelete();
        });
    }
}; 