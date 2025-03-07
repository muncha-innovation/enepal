<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add indexes to posts table
        Schema::table('posts', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('business_id');
            $table->index('created_at');
            $table->index(['title']); // For search queries
        });

        // Add indexes to likes table
        Schema::table('likes', function (Blueprint $table) {
            $table->index(['post_id', 'user_id']);
        });

        // Add indexes to comments table
        Schema::table('comments', function (Blueprint $table) {
            $table->index(['post_id', 'created_at']);
            $table->index('user_id');
        });

        // Add indexes to businesses table
        Schema::table('businesses', function (Blueprint $table) {
            $table->index('type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove indexes
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['business_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['title']);
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->dropIndex(['post_id', 'user_id']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['post_id', 'created_at']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex(['type_id']);
        });
    }
}
