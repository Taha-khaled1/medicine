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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar', 100);
            $table->string('name_en', 100)->nullable();
            $table->string('image')->nullable();
            $table->float('latitude', 100)->nullable();
            $table->float('longitude', 100)->nullable();
            $table->string('symbol_ar'); // like درهم
            $table->string('symbol_en'); // like AED
            $table->string('code');  // like +20
            $table->decimal('exchange_rate', 15, 6); // Exchange rate
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
