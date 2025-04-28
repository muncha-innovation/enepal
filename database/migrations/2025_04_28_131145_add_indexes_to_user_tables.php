<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index(['first_name', 'last_name'], 'idx_users_name');
            $table->index('phone', 'idx_users_phone');
            $table->index('is_active', 'idx_users_active');
            $table->index('created_by', 'idx_users_created_by');
            $table->index('email_verified_at', 'idx_users_email_verified');
            $table->index('phone_verified_at', 'idx_users_phone_verified');
        });

        // Add indexes to user_preferences table
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->index('user_id', 'idx_uprefs_user_id');
            $table->index('user_type', 'idx_uprefs_user_type');
            $table->index('app_language', 'idx_uprefs_app_lang');
            $table->index(['receive_notifications', 'show_personalized_content'], 'idx_uprefs_notifications_content');
        });

        // Add indexes to user_experiences table
        Schema::table('user_experiences', function (Blueprint $table) {
            $table->index('user_id', 'idx_user_exp_user_id');
            $table->index(['start_date', 'end_date'], 'idx_user_exp_dates');
            $table->index('company', 'idx_user_exp_company');
        });

        // Add indexes to user_education table
        Schema::table('user_education', function (Blueprint $table) {
            $table->index('user_id', 'idx_user_edu_user_id');
            $table->index('type', 'idx_user_edu_type');
            $table->index(['start_date', 'end_date'], 'idx_user_edu_dates');
            $table->index('institution', 'idx_user_edu_institution');
        });

        // Add indexes to user_genders table
        Schema::table('user_genders', function (Blueprint $table) {
            $table->index('name', 'idx_user_genders_name');
        });

        // Add indexes to addresses (for user addresses)
        Schema::table('addresses', function (Blueprint $table) {
            $table->index(['addressable_id', 'addressable_type'], 'idx_addresses_addressable');
            $table->index('country_id', 'idx_addresses_country');
            $table->index('state_id', 'idx_addresses_state');
            $table->index('address_type', 'idx_addresses_type');
            $table->index('city', 'idx_addresses_city');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_name');
            $table->dropIndex('idx_users_phone');
            $table->dropIndex('idx_users_active');
            $table->dropIndex('idx_users_created_by');
            $table->dropIndex('idx_users_email_verified');
            $table->dropIndex('idx_users_phone_verified');
        });

        // Remove indexes from user_preferences table
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropIndex('idx_uprefs_user_id');
            $table->dropIndex('idx_uprefs_user_type');
            $table->dropIndex('idx_uprefs_app_lang');
            $table->dropIndex('idx_uprefs_notifications_content');
        });

        // Remove indexes from user_experiences table
        Schema::table('user_experiences', function (Blueprint $table) {
            $table->dropIndex('idx_user_exp_user_id');
            $table->dropIndex('idx_user_exp_dates');
            $table->dropIndex('idx_user_exp_company');
        });

        // Remove indexes from user_education table
        Schema::table('user_education', function (Blueprint $table) {
            $table->dropIndex('idx_user_edu_user_id');
            $table->dropIndex('idx_user_edu_type');
            $table->dropIndex('idx_user_edu_dates');
            $table->dropIndex('idx_user_edu_institution');
        });

        // Remove indexes from user_genders table
        Schema::table('user_genders', function (Blueprint $table) {
            $table->dropIndex('idx_user_genders_name');
        });

        // Remove indexes from addresses (for user addresses)
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropIndex('idx_addresses_addressable');
            $table->dropIndex('idx_addresses_country');
            $table->dropIndex('idx_addresses_state');
            $table->dropIndex('idx_addresses_type');
            $table->dropIndex('idx_addresses_city');
        });
    }
}
