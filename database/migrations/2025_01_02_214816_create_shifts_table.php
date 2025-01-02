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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->time('start_time');
            $table->time('end_time');
            $table->date('shift_date')->default(now());
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->decimal('initial_amount', 10, 2)->default(0); // Money before shift
            $table->decimal('remaining_amount', 10, 2)->nullable(); // Remaining money
            $table->decimal('unpaid_amount', 10, 2)->default(0); // Unpaid money
            $table->decimal('actual_amount', 10, 2)->nullable(); // Actually delivered
            $table->decimal('total_amount', 10, 2)->default(0); // Total money(); // Actually delivered
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
