<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
            [
                "id" => 1,
                'name_ar' => 'الإمارات العربية المتحدة',
                'name_en' => 'United Arab Emirates',
                'image' => 'https://app.automark.site/upload/flags/Flag-of-United-Arab-Emirates.png',
                'latitude' => 25.276987,
                'longitude' => 55.296249,
                'code' => '+971',
                'symbol_ar' => 'درهم',
                'symbol_en' => 'AED',
                'exchange_rate' => 1,
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(30),
            ],
            [
                "id" => 2,
                'name_ar' => 'المملكة العربية السعودية',
                'name_en' => 'Saudi Arabia',
                'image' => 'https://app.automark.site/upload/flags/ksa.png',
                'latitude' => 24.774265,
                'longitude' => 46.738586,
                'code' => '+966',
                'symbol_ar' => 'ريال',
                'symbol_en' => 'SAR',
                'exchange_rate' => 1.02,
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(29),
            ],
            [
                "id" => 3,
                'name_ar' => 'سلطنة عمان',
                'name_en' => 'Oman',
                'image' => 'https://app.automark.site/upload/flags/oman.png',
                'latitude' => 23.614328,
                'longitude' => 58.545284,
                'code' => '+968',
                'symbol_ar' => 'ريال عماني',
                'symbol_en' => 'OMR',
                'exchange_rate' => 9.45,
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(28),

            ],
            [
                "id" => 4,
                'name_ar' => 'دولة الكويت',
                'name_en' => 'Kuwait',
                'image' => 'https://app.automark.site/upload/flags/kw.png',
                'latitude' => 29.378586,
                'longitude' => 47.990341,
                'code' => '+965',
                'symbol_ar' => 'دينار كويتي',
                'symbol_en' => 'KWD',
                'exchange_rate' => 0.084,
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(27),

            ],
            [
                "id" => 5,
                'name_ar' => 'دولة قطر',
                'name_en' => 'Qatar',
                'image' => 'https://app.automark.site/upload/flags/Flag_of_Qatar.svg.png',
                'latitude' => 25.286106,
                'longitude' => 51.534817,
                'code' => '+974',
                'symbol_ar' => 'ريال قطري',
                'symbol_en' => 'QA',
                'exchange_rate' => 1,
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(26),

            ],
            [
                "id" => 6,
                'name_ar' => 'جمهورية العراق',
                'name_en' => 'Iraq',
                'image' => 'https://app.automark.site/upload/flags/Flag_of_Iraq.svg.png',
                'latitude' => 33.312805,
                'longitude' => 44.361488,
                'code' => '+964',
                'symbol_ar' => 'دينار عراقي',
                'symbol_en' => 'IQD',
                'exchange_rate' => 356.02,
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(25),

            ],
            [
                "id" => 7,
                'name_ar' => 'مملكة البحرين',
                'name_en' => 'Bahrain',
                'image' => 'https://app.automark.site/upload/flags/bahreen.png',
                'latitude' => 26.201,
                'longitude' => 50.606998,
                'code' => '+973',
                'symbol_ar' => 'دينار بحريني',
                'symbol_en' => 'BHD',
                'exchange_rate' => 0.1,
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(24),

            ],
            [
                "id" => 8,
                'name_ar' => 'المملكة الأردنية الهاشمية',
                'name_en' => 'Jordan',
                'image' => 'https://app.automark.site/upload/flags/Flag_of_Jordan.svg.png',
                'latitude' => 31.963158,
                'longitude' => 35.930359,
                'code' => '+962',
                'symbol_ar' => 'دينار اردني',
                'symbol_en' => 'JOD',
                'exchange_rate' => 0.19,
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(23),

            ],
            [
                "id" => 9,
                'name_ar' => 'جمهورية مصر العربية',
                'name_en' => 'Egypt',
                'image' => 'https://app.automark.site/upload/flags/egypt.jpg',
                'latitude' => 30.033333,
                'longitude' => 31.233334,
                'code' => '+20',
                'symbol_ar' => 'جنية',
                'symbol_en' => 'EGP',
                'exchange_rate' => 8.4,
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(22),

            ],
            //-------------------------------------
            [
                "id" => 10,
                'name_ar' => 'الولايات المتحدة الأمريكية',
                'name_en' => 'United States of America',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Flag_of_the_United_States.svg/120px-Flag_of_the_United_States.svg.png',
                'latitude' => 37.0902,
                'longitude' => -95.7129,
                'code' => '+1',
                'symbol_ar' => 'دولار',
                'symbol_en' => 'USD',
                'exchange_rate' => 3.67, // Example exchange rate
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(30),
            ],

            [
                "id" => 11,
                'name_ar' => 'ألمانيا',
                'name_en' => 'Germany',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Flag_of_Germany.svg/120px-Flag_of_Germany.svg.png',
                'latitude' => 51.1657,
                'longitude' => 10.4515,
                'code' => '+49',
                'symbol_ar' => 'يورو',
                'symbol_en' => 'EUR',
                'exchange_rate' => 4.0, // Example exchange rate
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(30),
            ],
            [
                "id" => 12,
                'name_ar' => 'إيطاليا',
                'name_en' => 'Italy',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/03/Flag_of_Italy.svg/120px-Flag_of_Italy.svg.png',
                'latitude' => 41.8719,
                'longitude' => 12.5674,
                'code' => '+39',
                'symbol_ar' => 'يورو',
                'symbol_en' => 'EUR',
                'exchange_rate' => 4.0, // Example exchange rate
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(30),
            ],
            [
                "id" => 13,
                'name_ar' => 'إسبانيا',
                'name_en' => 'Spain',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Flag_of_Spain.svg/120px-Flag_of_Spain.svg.png',
                'latitude' => 40.4637,
                'longitude' => -3.7492,
                'code' => '+34',
                'symbol_ar' => 'يورو',
                'symbol_en' => 'EUR',
                'exchange_rate' => 4.0, // Example exchange rate
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(30),
            ],
            [
                "id" => 14,
                'name_ar' => 'تركيا',
                'name_en' => 'Turkey',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b4/Flag_of_Turkey.svg/120px-Flag_of_Turkey.svg.png',
                'latitude' => 38.9637,
                'longitude' => 35.2433,
                'code' => '+90',
                'symbol_ar' => 'ليرة',
                'symbol_en' => 'TRY',
                'exchange_rate' => 0.13, // Example exchange rate
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(30),
            ],
            [
                "id" => 15,
                'name_ar' => 'كندا',
                'name_en' => 'Canada',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cf/Flag_of_Canada.svg/120px-Flag_of_Canada.svg.png',
                'latitude' => 56.1304,
                'longitude' => -106.3468,
                'code' => '+1',
                'symbol_ar' => 'دولار',
                'symbol_en' => 'CAD',
                'exchange_rate' => 2.75, // Example exchange rate
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(30),
            ],
            [
                "id" => 16,
                'name_ar' => 'أستراليا',
                'name_en' => 'Australia',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b9/Flag_of_Australia.svg/120px-Flag_of_Australia.svg.png',
                'latitude' => -25.2744,
                'longitude' => 133.7751,
                'code' => '+61',
                'symbol_ar' => 'دولار',
                'symbol_en' => 'AUD',
                'exchange_rate' => 2.65, // Example exchange rate
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(30),
            ],
            [
                "id" => 17,
                'name_ar' => 'النرويج',
                'name_en' => 'Norway',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d9/Flag_of_Norway.svg/120px-Flag_of_Norway.svg.png',
                'latitude' => 60.4720,
                'longitude' => 8.4689,
                'code' => '+47',
                'symbol_ar' => 'كرونة',
                'symbol_en' => 'NOK',
                'exchange_rate' => 4.0, // Example exchange rate
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(30),
            ],
            [
                "id" => 18,
                'name_ar' => 'نيوزيلندا',
                'name_en' => 'New Zealand',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Flag_of_New_Zealand.svg/260px-Flag_of_New_Zealand.svg.png',
                'latitude' => -40.9006,
                'longitude' => 174.886,
                'code' => '+64',
                'symbol_ar' => 'دولار',
                'symbol_en' => 'NZD',
                'exchange_rate' => 2.5, // Example exchange rate
                // 'country_tax' => 1,
                // "created_at" => now()->subDays(30),
            ],
        ];

        // Insert data into the countries_admins table
        foreach ($countries as $country) {
            DB::table('countries')->insert($country);
        }
    }
}
