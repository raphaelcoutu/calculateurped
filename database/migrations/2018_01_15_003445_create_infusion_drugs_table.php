<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfusionDrugsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infusion_drugs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('concentration');
            $table->float('debit_min');
            $table->float('debit_max');
            $table->string('debit_dose_unit');
            $table->string('debit_time_unit');
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
        Schema::dropIfExists('infusion_drugs');
    }
}
