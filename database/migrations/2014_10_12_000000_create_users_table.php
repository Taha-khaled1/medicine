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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable()->unique();
            $table->string('password');
            $table->enum('login_type', ['google', 'apple', 'facebook', 'normal'])->default('normal');
            $table->string('image')->nullable();
            $table->string('fcm')->nullable();
            $table->enum('gander', ['male', 'female'])->default('male');
            $table->string('date'); // like 2000-01-01
            $table->string('code')->nullable();
            $table->boolean('status')->default(true);
            $table->string('invitation_code')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
