<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bolus_activity', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('bolus_id');
            $table->foreign('bolus_id')->references('id')->on('boluses')->cascadeOnDelete();
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('action', 32);
            $table->json('changes')->nullable();
            $table->timestamps();
            $table->index(['bolus_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * This drops the bolus audit history. Use a forward migration instead of rolling
     * back this feature in production.
     */
    public function down(): void
    {
        Schema::dropIfExists('bolus_activity');
    }
};
