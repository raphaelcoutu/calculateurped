<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfusionConcentrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infusion_concentrations', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('infusion_drug_id');
            $table->float('concentration');
            $table->string('concentration_unit');
            $table->string('instructions');
            $table->float('total_volume');
            $table->tinyInteger('weight_category');
            $table->timestamps();

            // Weight category:
            // 1: [0, 5] kg
            // 2: ]5, 10] kg
            // 3: ]10, 15] kg
            // 4: ]15, 35[ kg
            // 5: [35, infinity[ kg
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infusion_concentrations');
    }
}
