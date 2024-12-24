<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'title' => 'Technology',
                'description' => 'Technology',
            ],
        ];

        // Insert data into the categories table
        foreach ($categories as $category) {
            DB::table('categories')->insert($category);
        }
    }
}
