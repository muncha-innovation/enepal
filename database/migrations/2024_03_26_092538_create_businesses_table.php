<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->unsignedMediumInteger('type_id')->nullable();
            $table->string('email', 100)->unique()->nullable();      
            $table->string('phone_1', 20)->nullable();
            $table->string('phone_2', 20)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->json('social_media')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('contact_person_id')->nullable();
            $table->integer('established_year')->nullable();
            $table->text('custom_email_message')->nullable();

            $table->foreign('type_id')->references('id')->on('business_types')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('contact_person_id')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('businesses');
    }
}
