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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->index()->constrained();
            $table->foreignId('user_id')->index()->constrained();
            $table->foreignId('project_id')->index()->nullable()->constrained()->nullOnDelete();
            $table->dateTime('date');
            $table->integer('amount');
            $table->foreignId('currency_id')->default(1)->constrained('currencies');
            $table->string('notes')->nullable();
            $table->boolean('is_regular')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
