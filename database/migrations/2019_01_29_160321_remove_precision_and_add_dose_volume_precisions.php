<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePrecisionAndAddDoseVolumePrecisions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boluses', function (Blueprint $table) {
            $table->tinyInteger('dose_precision')->default(1)->after('maximum_dose');
            $table->tinyInteger('volume_precision')->default(1)->after('dose_precision');
            $table->dropColumn('precision');
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
            $table->tinyInteger('precision')->after('maximum_dose');
            $table->dropColumn('dose_precision');
            $table->dropColumn('volume_precision');
        });
    }
}
