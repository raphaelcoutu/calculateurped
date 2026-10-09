<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_profiles', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('organization_id');
            $table->foreign('organization_id')->references('id')->on('organizations')->restrictOnDelete();
            $table->uuid('profile_id');
            $table->unsignedInteger('version')->default(1);
            $table->string('name', 191);
            $table->string('status', 20)->default('draft');
            foreach (['created_by', 'published_by'] as $column) {
                $table->unsignedInteger($column)->nullable();
                $table->foreign($column)->references('id')->on('users')->nullOnDelete();
            }
            $table->unsignedInteger('supersedes_id')->nullable();
            $table->foreign('supersedes_id')->references('id')->on('prescription_profiles')->restrictOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('superseded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['profile_id', 'version']);
            $table->index(['organization_id', 'status', 'deleted_at']);
        });
        Schema::table('organizations', function (Blueprint $table): void {
            $table->unsignedInteger('default_prescription_profile_id')->nullable();
            $table->foreign('default_prescription_profile_id')->references('id')->on('prescription_profiles')->restrictOnDelete();
        });
        Schema::create('prescription_sections', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('prescription_profile_id');
            $table->foreign('prescription_profile_id')->references('id')->on('prescription_profiles')->cascadeOnDelete();
            $table->string('name', 191);
            $table->unsignedInteger('position');
            $table->timestamps();
            $table->index(['prescription_profile_id', 'position']);
        });
        Schema::create('prescription_items', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('prescription_section_id');
            $table->foreign('prescription_section_id')->references('id')->on('prescription_sections')->cascadeOnDelete();
            $table->unsignedInteger('bolus_id')->nullable();
            $table->foreign('bolus_id')->references('id')->on('boluses')->restrictOnDelete();
            $table->unsignedInteger('infusion_drug_id')->nullable();
            $table->foreign('infusion_drug_id')->references('id')->on('infusion_drugs')->restrictOnDelete();
            $table->unsignedInteger('position');
            $table->timestamps();
            $table->index(['prescription_section_id', 'position']);
            $table->index('bolus_id');
            $table->index('infusion_drug_id');
        });
        Schema::create('prescription_activity', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('prescription_profile_id');
            $table->foreign('prescription_profile_id')->references('id')->on('prescription_profiles')->restrictOnDelete();
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('action', 40);
            $table->json('changes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_activity');
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescription_sections');
        Schema::table('organizations', function (Blueprint $table): void {
            $table->dropForeign(['default_prescription_profile_id']);
            $table->dropColumn('default_prescription_profile_id');
        });
        Schema::dropIfExists('prescription_profiles');
    }
};
