<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $view = null;
        $grants = [];
        if (DB::getDriverName() === 'pgsql' && Schema::hasView('vw_infusions')) {
            $view = DB::selectOne("SELECT pg_get_viewdef('vw_infusions'::regclass) AS definition")->definition;
            $grants = DB::select("SELECT grantee, privilege_type, is_grantable FROM information_schema.role_table_grants WHERE table_schema = current_schema() AND table_name = 'vw_infusions'");
            DB::statement('DROP VIEW vw_infusions');
        }

        Schema::table('infusion_concentrations', function (Blueprint $table): void {
            $table->unsignedInteger('infusion_drug_id')->change();
            $table->float('min_weight')->default(0);
            $table->float('max_weight')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->tinyInteger('weight_category')->default(0)->change();
            $table->foreign('infusion_drug_id')->references('id')->on('infusion_drugs')->cascadeOnDelete();
            $table->index(['infusion_drug_id', 'position']);
        });

        if ($view !== null) {
            DB::statement('CREATE VIEW vw_infusions AS '.$view);
            foreach ($grants as $grant) {
                $role = $grant->grantee === 'PUBLIC' ? 'PUBLIC' : '"'.str_replace('"', '""', $grant->grantee).'"';
                DB::statement('GRANT '.$grant->privilege_type.' ON vw_infusions TO '.$role.($grant->is_grantable === 'YES' ? ' WITH GRANT OPTION' : ''));
            }
        }

        foreach ([1 => [0, 5], 2 => [5, 10], 3 => [10, 15], 4 => [15, 35], 5 => [35, null]] as $category => [$min, $max]) {
            DB::table('infusion_concentrations')->where('weight_category', $category)->update([
                'min_weight' => $min, 'max_weight' => $max, 'position' => $category,
            ]);
        }
    }

    /**
     * Le retour arrière supprime les plages et leur ordre, qui ne peuvent pas être
     * représentés fidèlement par les anciennes catégories. Préférer une migration corrective.
     */
    public function down(): void
    {
        Schema::table('infusion_concentrations', function (Blueprint $table): void {
            $table->dropForeign(['infusion_drug_id']);
            $table->dropIndex(['infusion_drug_id', 'position']);
            $table->dropColumn(['min_weight', 'max_weight', 'position']);
        });
    }
};
