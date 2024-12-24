<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Faq::create([
            'question' => 'What is the purpose of this application?',
            'answer' => 'The purpose of this application is to...'
        ]);

        Faq::create([
            'question' => 'How do I get started?',
            'answer' => 'To get started, you need to...'
        ]);
    }
}
