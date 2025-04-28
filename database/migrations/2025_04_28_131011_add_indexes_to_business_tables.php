<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToBusinessTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add indexes to businesses table
        Schema::table('businesses', function (Blueprint $table) {
            $table->index('name', 'idx_biz_name');
            $table->index('phone_1', 'idx_biz_phone');
            $table->index(['is_verified', 'is_active'], 'idx_biz_status');
            $table->index('created_by', 'idx_biz_created_by');
        });

        // Add indexes to business_hours table
        Schema::table('business_hours', function (Blueprint $table) {
            $table->index(['business_id', 'day'], 'idx_biz_hours');
        });

        // Add indexes to business_facilities table
        Schema::table('business_facilities', function (Blueprint $table) {
            $table->index(['business_id', 'facility_id'], 'idx_biz_facilities');
        });


        // Add indexes to business_languages table
        Schema::table('business_languages', function (Blueprint $table) {
            $table->index(['business_id', 'language_id'], 'idx_biz_languages');
            $table->index('type', 'idx_biz_lang_type');
        });

        // Add indexes to business_social_networks table
        Schema::table('business_social_networks', function (Blueprint $table) {
            $table->index(['business_id', 'social_network_id'], 'idx_biz_socials');
            $table->index('is_active', 'idx_biz_social_active');
        });

        // Add indexes to business_user table
        Schema::table('business_user', function (Blueprint $table) {
            $table->index(['business_id', 'role'], 'idx_biz_user_biz_role');
            $table->index(['user_id', 'role'], 'idx_biz_user_user_role');
            $table->index('is_active', 'idx_biz_user_active');
        });

        // Add indexes to business_settings table
        Schema::table('business_settings', function (Blueprint $table) {
            $table->index(['business_id', 'key'], 'idx_biz_settings');
            $table->index('type', 'idx_biz_settings_type');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove indexes from businesses table
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex('idx_biz_name');
            $table->dropIndex('idx_biz_email');
            $table->dropIndex('idx_biz_phone');
            $table->dropIndex('idx_biz_status');
            $table->dropIndex('idx_biz_created_by');
            $table->dropIndex('idx_biz_type');
        });

        // Remove indexes from business_hours table
        Schema::table('business_hours', function (Blueprint $table) {
            $table->dropIndex('idx_biz_hours');
        });

        // Remove indexes from business_facilities table
        Schema::table('business_facilities', function (Blueprint $table) {
            $table->dropIndex('idx_biz_facilities');
        });

        // Remove indexes from business_destinations table
        Schema::table('business_destinations', function (Blueprint $table) {
            $table->dropIndex('idx_biz_destinations');
        });

        // Remove indexes from business_languages table
        Schema::table('business_languages', function (Blueprint $table) {
            $table->dropIndex('idx_biz_languages');
            $table->dropIndex('idx_biz_lang_type');
        });

        // Remove indexes from business_social_networks table
        Schema::table('business_social_networks', function (Blueprint $table) {
            $table->dropIndex('idx_biz_socials');
            $table->dropIndex('idx_biz_social_active');
        });

        // Remove indexes from business_user table
        Schema::table('business_user', function (Blueprint $table) {
            $table->dropIndex('idx_biz_user_biz_role');
            $table->dropIndex('idx_biz_user_user_role');
            $table->dropIndex('idx_biz_user_active');
        });

        // Remove indexes from business_settings table
        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropIndex('idx_biz_settings');
            $table->dropIndex('idx_biz_settings_type');
        });

        // Remove indexes from business_vendor table
        Schema::table('business_vendor', function (Blueprint $table) {
            $table->dropIndex('idx_biz_vendor');
        });
    }
}
