<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingWebTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            DB::table('setting_webs')->insert([
            'about_us_ar' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ...',
            'about_us_en' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ...',
            'terms_ar' => 'Terms in Arabic...',
            'terms_en' => 'Terms in English...',
            'privacy_ar' => 'Privacy Policy in Arabic...',
            'privacy_en' => 'Privacy Policy in English...',
            'return_policy_ar' => 'Return Policy in Arabic...',
            'return_policy_en' => 'Return Policy in English...',
            'store_policy_ar' => 'Store Policy in Arabic...',
            'store_policy_en' => 'Store Policy in English...',
            'seller_policy_ar' => 'Seller Policy in Arabic...',
            'seller_policy_en' => 'Seller Policy in English...',
            'color_primery' => '#ffffff',
            'color_second_primery' => '#000000',
            'licance_web' => 'License code here',
            'banner' => 'banner.jpg',

        ]);
    }
}