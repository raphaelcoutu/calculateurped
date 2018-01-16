<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBolusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boluses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('unit');
            $table->float('commercial_concentration');
            $table->float('dosage');
            $table->float('minimum_dose');
            $table->float('maximum_dose');
            $table->tinyInteger('type');
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
        Schema::dropIfExists('boluses');
    }
}
