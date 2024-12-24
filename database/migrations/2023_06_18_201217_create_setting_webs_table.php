<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_webs', function (Blueprint $table) {
            $table->id();
            $table->longText('about_us_ar')->nullable();
            $table->longText('about_us_en')->nullable();
            $table->longText('terms_ar')->nullable();
            $table->longText('terms_en')->nullable();
            $table->longText('privacy_ar')->nullable();
            $table->longText('privacy_en')->nullable();
            $table->longText('return_policy_ar')->nullable();
            $table->longText('return_policy_en')->nullable();
            $table->longText('store_policy_ar')->nullable();
            $table->longText('store_policy_en')->nullable();
            $table->longText('seller_policy_ar')->nullable();
            $table->longText('seller_policy_en')->nullable();
            $table->string('color_primery')->nullable();
            $table->string('color_second_primery')->nullable();
            $table->string('licance_web')->nullable();
            $table->string('banner')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_webs');
    }
};
