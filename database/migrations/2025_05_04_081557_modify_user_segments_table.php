<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('segment_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_segment_id')->constrained('user_segments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_segment_id', 'user_id']);
        });

        Schema::table('user_segments', function (Blueprint $table) {
            $table->dropColumn('conditions');
            
            $table->string('type')->default('custom')->after('description');
            $table->boolean('is_default')->default(false)->after('type');
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('segment_user');
        
        Schema::table('user_segments', function (Blueprint $table) {
            if (Schema::hasColumn('user_segments', 'type')) {
                $table->dropColumn('type');
            }
            
            if (!Schema::hasColumn('user_segments', 'conditions')) {
                $table->json('conditions')->nullable();
            }
        });
    }
};
