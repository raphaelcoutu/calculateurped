<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMinMaxWeightToBolusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boluses', function (Blueprint $table) {
            $table->decimal('min_weight')->default(0.0)->after('type');
            $table->decimal('max_weight')->default(999.0)->after('min_weight');
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
            $table->dropColumn(['min_weight', 'max_weight']);
        });
    }
}
