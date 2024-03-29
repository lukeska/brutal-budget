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
        Schema::create('onboarding_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->index()->constrained()->cascadeOnDelete();
            $table->string('onboarding_step', 50);
            $table->tinyInteger('order');
            $table->timestamp('skipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_statuses');
    }
};
