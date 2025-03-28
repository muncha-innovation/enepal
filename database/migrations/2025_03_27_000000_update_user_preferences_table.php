<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::table('user_preferences', function (Blueprint $table) {
            
            $table->string('user_type')->nullable()->after('user_id');
            
            $table->json('known_languages')->nullable()->after('user_type');
            
            $table->boolean('has_passport')->nullable()->after('known_languages');
            $table->date('passport_expiry')->nullable()->after('has_passport');
           
            $table->boolean('receive_notifications')->default(true);
            $table->boolean('show_personalized_content')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropColumn([
                'user_type',
                
                'known_languages',
               
                'has_passport',
                'passport_expiry',
                
                'receive_notifications',
                'show_personalized_content'
            ]);
        });
    }
}