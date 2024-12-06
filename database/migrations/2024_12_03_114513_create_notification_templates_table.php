<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->text('name');
            $table->text('subject');
            $table->string('push_title')->nullable();
            $table->text('email_body')->nullable();
            $table->text('sms_body')->nullable();
            $table->text('push_body')->nullable();
            $table->text('placeholders')->nullable()->comment('json encoded array in format {name: "Name", description: "Description"}');
            $table->boolean('email_status')->default(1);
            $table->boolean('sms_status')->default(0);
            $table->boolean('push_status')->default(0);
            $table->string('email_sent_from_name')->nullable();
            $table->string('email_sent_from_email')->nullable();
            $table->string('sms_sent_from')->nullable();
            $table->boolean('allow_business_section')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_templates');
    }
}
