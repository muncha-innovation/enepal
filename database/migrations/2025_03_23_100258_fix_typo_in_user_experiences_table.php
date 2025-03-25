<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTypoInUserExperiencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_experiences', function (Blueprint $table) {
            //change descriotion to description
            $table->renameColumn('descriptioin', 'description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_experiences', function (Blueprint $table) {
            //
            $table->renameColumn('description', 'descriptioin');
        });
    }
}
