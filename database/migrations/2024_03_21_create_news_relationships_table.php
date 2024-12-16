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

        // First check if the foreign key exists before trying to drop it
        Schema::table('news_items', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $foreignKeys = $sm->listTableForeignKeys('news_items');
            $foreignKeyExists = collect($foreignKeys)->contains(function($fk) {
                return $fk->getName() === 'news_items_main_news_id_foreign';
            });

            if ($foreignKeyExists) {
                $table->dropForeign('news_items_main_news_id_foreign');
            }

            if (Schema::hasColumn('news_items', 'main_news_id')) {
                $table->dropColumn('main_news_id');
            }
        });
    }

    public function down()
    {
        // First drop the relationships table
        Schema::dropIfExists('news_relationships');
        
        // Then add back the main_news_id column
        Schema::table('news_items', function (Blueprint $table) {
            $table->foreignId('main_news_id')->nullable()->constrained('news_items')->nullOnDelete();
        });
    }
}; 