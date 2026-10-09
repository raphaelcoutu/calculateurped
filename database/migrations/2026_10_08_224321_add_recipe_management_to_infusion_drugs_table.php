<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('infusion_drugs', function (Blueprint $table): void {
            $table->unsignedInteger('organization_id')->nullable()->after('id');
            $table->uuid('recipe_id')->nullable()->after('organization_id');
            $table->unsignedInteger('version')->default(1)->after('recipe_id');
            $table->string('status', 20)->default('draft')->after('version');
            $table->unsignedInteger('created_by')->nullable()->after('status');
            $table->unsignedInteger('published_by')->nullable()->after('created_by');
            $table->timestamp('published_at')->nullable()->after('published_by');
            $table->timestamp('superseded_at')->nullable()->after('published_at');
            $table->unsignedInteger('supersedes_id')->nullable()->after('superseded_at');
            $table->softDeletes();
        });

        $organizationId = DB::table('organizations')
            ->where('name', 'CIUSSS de l’Estrie–CHUS')
            ->value('id');

        if ($organizationId === null) {
            $organizationId = DB::table('organizations')->insertGetId([
                'name' => 'CIUSSS de l’Estrie–CHUS',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('infusion_drugs')->orderBy('id')->get(['id'])->each(function (object $drug) use ($organizationId): void {
            DB::table('infusion_drugs')->where('id', $drug->id)->update([
                'organization_id' => $organizationId,
                'recipe_id' => (string) Str::uuid(),
                'status' => 'published',
                'published_at' => now(),
            ]);
        });

        Schema::table('infusion_drugs', function (Blueprint $table): void {
            $table->unsignedInteger('organization_id')->nullable(false)->change();
            $table->uuid('recipe_id')->nullable(false)->change();
            $table->foreign('organization_id')->references('id')->on('organizations')->restrictOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('published_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('supersedes_id')->references('id')->on('infusion_drugs')->nullOnDelete();
            $table->index(['organization_id', 'status', 'deleted_at']);
            $table->unique(['recipe_id', 'version']);
            $table->index(['status', 'published_at', 'superseded_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * Dropping these columns discards organization ownership and drug version metadata;
     * use a forward migration instead of rolling back this feature in production.
     */
    public function down(): void
    {
        Schema::table('infusion_drugs', function (Blueprint $table): void {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['supersedes_id']);
            $table->dropIndex(['organization_id', 'status', 'deleted_at']);
            $table->dropUnique(['recipe_id', 'version']);
            $table->dropIndex(['status', 'published_at', 'superseded_at']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['published_by']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'organization_id',
                'recipe_id',
                'version',
                'status',
                'created_by',
                'published_by',
                'published_at',
                'superseded_at',
                'supersedes_id',
            ]);
        });
    }
};
