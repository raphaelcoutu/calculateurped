<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDosagePrecisionToInfusionDrugsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('infusion_drugs', function (Blueprint $table) {
            $table->tinyInteger('dosage_precision')->default(1)->after('debit_limit_unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('infusion_drugs', function (Blueprint $table) {
            $table->dropColumn('dosage_precision');
        });
    }
}
