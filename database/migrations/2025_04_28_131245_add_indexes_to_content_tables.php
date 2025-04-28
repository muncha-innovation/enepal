<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToContentTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add indexes to news_items table
        Schema::table('news_items', function (Blueprint $table) {
            $table->index(['sourceable_id', 'sourceable_type'], 'idx_news_source');
            $table->index('is_active', 'idx_news_active');
            $table->index('is_featured', 'idx_news_featured');
            $table->index('language', 'idx_news_language');
            $table->index('published_at', 'idx_news_published');
            $table->index('views_count', 'idx_news_views');
        });

        // Add indexes to posts table
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['user_id', 'business_id'], 'idx_posts_user_biz');
            $table->index('is_active', 'idx_posts_active');
            $table->fullText('content', 'idx_posts_content_ft');
        });

        // Add indexes to products table
        Schema::table('products', function (Blueprint $table) {
            $table->index('business_id', 'idx_prod_biz_id');
            $table->index('created_by', 'idx_prod_created_by');
            $table->index('is_active', 'idx_prod_active');
            $table->fullText('name', 'idx_prod_name_ft');
            $table->fullText('description', 'idx_prod_desc_ft');
        });

        // Add indexes to comments table
        Schema::table('comments', function (Blueprint $table) {
            $table->index(['post_id', 'user_id'], 'idx_comments_post_user');
            $table->index('created_at', 'idx_comments_created');
        });

        // Add indexes to conversations and messages tables
        Schema::table('conversations', function (Blueprint $table) {
            $table->index(['business_id', 'user_id'], 'idx_conv_biz_user');
            $table->index('vendor_id', 'idx_conv_vendor');
            $table->index('last_message_at', 'idx_conv_last_msg');
            $table->index('is_active', 'idx_conv_active');
        });

        Schema::table('threads', function (Blueprint $table) {
            $table->index('conversation_id', 'idx_threads_conv');
            $table->index('status', 'idx_threads_status');
            $table->index('last_message_at', 'idx_threads_last_msg');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index('thread_id', 'idx_msg_thread');
            $table->index('conversation_id', 'idx_msg_conv');
            $table->index('created_at', 'idx_msg_created');
        });

        // Add indexes to business_notifications and business_notifications_users tables
        Schema::table('business_notifications', function (Blueprint $table) {
            $table->index('created_at', 'idx_biz_notif_created');
        });

        // Add indexes to galleries and gallery_images
        Schema::table('galleries', function (Blueprint $table) {
            $table->index(['business_id', 'is_active'], 'idx_galleries_biz_active');
            $table->index('user_id', 'idx_galleries_user');
            $table->index('is_private', 'idx_galleries_private');
        });

        Schema::table('gallery_images', function (Blueprint $table) {
            $table->index(['gallery_id', 'business_id'], 'idx_gallery_img_gal_biz');
        });

        // Add indexes to notification templates
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->index('action', 'idx_notif_templ_action');
            $table->index(['email_status', 'sms_status', 'push_status'], 'idx_notif_templ_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove indexes from news_items table
        Schema::table('news_items', function (Blueprint $table) {
            $table->dropIndex('idx_news_source');
            $table->dropIndex('idx_news_active');
            $table->dropIndex('idx_news_featured');
            $table->dropIndex('idx_news_language');
            $table->dropIndex('idx_news_published');
            $table->dropIndex('idx_news_views');
        });

        // Remove indexes from posts table
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('idx_posts_user_biz');
            $table->dropIndex('idx_posts_active');
            $table->dropFullText('idx_posts_title_ft');
            $table->dropFullText('idx_posts_content_ft');
        });

        // Remove indexes from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_prod_biz_id');
            $table->dropIndex('idx_prod_created_by');
            $table->dropIndex('idx_prod_active');
            $table->dropFullText('idx_prod_name_ft');
            $table->dropFullText('idx_prod_desc_ft');
        });

        // Remove indexes from comments table
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('idx_comments_post_user');
            $table->dropIndex('idx_comments_created');
        });

        // Remove indexes from conversations and messages tables
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex('idx_conv_biz_user');
            $table->dropIndex('idx_conv_vendor');
            $table->dropIndex('idx_conv_last_msg');
            $table->dropIndex('idx_conv_active');
        });

        Schema::table('threads', function (Blueprint $table) {
            $table->dropIndex('idx_threads_conv');
            $table->dropIndex('idx_threads_status');
            $table->dropIndex('idx_threads_last_msg');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('idx_msg_thread');
            $table->dropIndex('idx_msg_conv');
            $table->dropIndex('idx_msg_created');
        });

        // Remove indexes from business_notifications and business_notifications_users tables
        Schema::table('business_notifications', function (Blueprint $table) {
            $table->dropIndex('idx_biz_notif_created');
        });

        // Remove indexes from galleries and gallery_images
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropIndex('idx_galleries_biz_active');
            $table->dropIndex('idx_galleries_user');
            $table->dropIndex('idx_galleries_private');
        });

        Schema::table('gallery_images', function (Blueprint $table) {
            $table->dropIndex('idx_gallery_img_gal_biz');
        });

        // Remove indexes from notification templates
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->dropIndex('idx_notif_templ_action');
            $table->dropIndex('idx_notif_templ_status');
        });
    }
}
