<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
        
            // Link to conversation
            $table->unsignedBigInteger('conversation_id');
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
        
            // Message sender (polymorphic)
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('sender_type'); // 'user', 'business', 'system'
        
            // Content
            $table->text('content')->nullable();
            $table->json('attachments')->nullable();
        
            // Flags
            $table->boolean('is_notification')->default(false);
            $table->boolean('is_read')->default(false);
        
            // Timestamps
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
