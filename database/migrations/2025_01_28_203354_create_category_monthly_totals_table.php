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
        Schema::create('category_monthly_totals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->index()->constrained();
            $table->foreignId('team_id')->index()->constrained();
            // TODO: use foreignIdFor
            $table->foreignId('user_id')->nullable()->index()->constrained();
            $table->integer('amount');
            $table->foreignId('currency_id')->default(1)->constrained('currencies');
            $table->mediumInteger(column: 'year_month', unsigned: true)->index();
            $table->boolean('is_regular')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_monthly_totals');
    }
};
