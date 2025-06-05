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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // First, Second, Enterprise
            $table->string('slug')->unique(); // first, second, enterprise
            $table->unsignedInteger('event_limit')->nullable(); // 3, 10, null (unlimited)
            $table->decimal('monthly_price', 8, 2)->nullable(); // Monthly price
            $table->decimal('annual_price', 8, 2)->nullable(); // Annual price (usually discounted)
            $table->json('features')->nullable(); // Future feature restrictions
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // For ordering plans
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
