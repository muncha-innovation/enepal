<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentManagementFieldsToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('user_id');
            $table->boolean('is_approved')->default(true)->after('comment');
            
            // Add foreign key for parent comment
            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');
            
            // Add indexes for better performance
            $table->index('parent_id');
            $table->index('is_approved');
            $table->index(['post_id', 'is_approved']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['is_approved']);
            $table->dropIndex(['post_id', 'is_approved']);
            $table->dropColumn(['parent_id', 'is_approved']);
        });
    }
}
