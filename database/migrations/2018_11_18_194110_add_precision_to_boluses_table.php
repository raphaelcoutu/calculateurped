<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrecisionToBolusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boluses', function (Blueprint $table) {
            $table->tinyInteger('precision')->default(1)->after('maximum_dose');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boluses', function (Blueprint $table) {
            $table->dropColumn('precision');
        });
    }
}
