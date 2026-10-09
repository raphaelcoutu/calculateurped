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
        Schema::table('boluses', function (Blueprint $table): void {
            $table->unsignedInteger('organization_id')->nullable()->after('id');
            $table->uuid('recipe_id')->nullable()->after('organization_id');
            $table->unsignedInteger('version')->default(1)->after('recipe_id');
            $table->string('status', 20)->default('draft')->after('version');
            $table->unsignedInteger('created_by')->nullable()->after('status');
            $table->unsignedInteger('published_by')->nullable()->after('created_by');
            $table->timestamp('published_at')->nullable()->after('published_by');
            $table->timestamp('superseded_at')->nullable()->after('published_at');
            $table->unsignedInteger('supersedes_id')->nullable()->after('superseded_at');
            $table->unsignedInteger('copied_from_id')->nullable()->after('supersedes_id');
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

        DB::table('boluses')->orderBy('id')->get(['id'])->each(function (object $bolus) use ($organizationId): void {
            DB::table('boluses')->where('id', $bolus->id)->update([
                'organization_id' => $organizationId,
                'recipe_id' => (string) Str::uuid(),
                'status' => 'published',
                'published_at' => now(),
            ]);
        });

        Schema::table('boluses', function (Blueprint $table): void {
            $table->unsignedInteger('organization_id')->nullable(false)->change();
            $table->uuid('recipe_id')->nullable(false)->change();
            $table->foreign('organization_id')->references('id')->on('organizations')->restrictOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('published_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('supersedes_id')->references('id')->on('boluses')->nullOnDelete();
            $table->foreign('copied_from_id')->references('id')->on('boluses')->nullOnDelete();
            $table->index(['organization_id', 'status', 'deleted_at']);
            $table->index(['recipe_id', 'version']);
            $table->index(['status', 'published_at', 'superseded_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * Dropping these columns discards organization ownership and bolus version metadata;
     * use a forward migration instead of rolling back this feature in production.
     */
    public function down(): void
    {
        Schema::table('boluses', function (Blueprint $table): void {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['supersedes_id']);
            $table->dropForeign(['copied_from_id']);
            $table->dropIndex(['organization_id', 'status', 'deleted_at']);
            $table->dropIndex(['recipe_id', 'version']);
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
                'copied_from_id',
            ]);
        });
    }
};
