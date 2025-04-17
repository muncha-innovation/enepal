<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('conversation_id');
            $table->string('status')->default('open'); // open, closed
            $table->text('description')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
        });

        // Modify messages table to reference threads instead of conversations directly
        Schema::table('messages', function (Blueprint $table) {
            // Add thread_id column
            $table->unsignedBigInteger('thread_id')->after('conversation_id')->nullable();
            $table->foreign('thread_id')->references('id')->on('threads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['thread_id']);
            $table->dropColumn('thread_id');
        });

        Schema::dropIfExists('threads');
    }
};