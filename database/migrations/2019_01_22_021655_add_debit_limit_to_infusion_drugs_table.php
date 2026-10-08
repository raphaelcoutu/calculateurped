<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDebitLimitToInfusionDrugsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('infusion_drugs', function (Blueprint $table) {
            $table->float('debit_min_limit')->default(0)->after('debit_time_unit');
            $table->float('debit_max_limit')->default(0)->after('debit_min_limit');
            $table->string('debit_limit_unit')->default('')->after('debit_max_limit');
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
            $table->dropColumn('debit_min_limit');
            $table->dropColumn('debit_max_limit');
            $table->dropColumn('debit_limit_unit');
        });
    }
}
