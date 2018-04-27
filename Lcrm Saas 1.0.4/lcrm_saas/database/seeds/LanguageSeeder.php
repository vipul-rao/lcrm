<?php

use Illuminate\Database\Seeder;
use App\Models\Option;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Eloquent::unguard();
        //priority options
        Option::create([
            'category' => 'language',
            'title' => 'English (en)',
            'value' => 'en',
        ]);
        Option::create([
            'category' => 'language',
            'title' => 'Brazilian Portuguese (pt_BR)',
            'value' => 'pt_BR',
        ]);
        Option::create([
            'category' => 'language',
            'title' => 'Chinese Simplified (zh)',
            'value' => 'zh',
        ]);
        Option::create([
            'category' => 'language',
            'title' => 'French (fr)',
            'value' => 'fr',
        ]);
        Option::create([
            'category' => 'language',
            'title' => 'German (de)',
            'value' => 'de',
        ]);
        Option::create([
            'category' => 'language',
            'title' => 'Japanese (ja)',
            'value' => 'ja',
        ]);
        Option::create([
            'category' => 'language',
            'title' => 'Romanian (ro)',
            'value' => 'ro',
        ]);
        Option::create([
            'category' => 'language',
            'title' => 'Russian (ru)',
            'value' => 'ru',
        ]);
        Option::create([
            'category' => 'language',
            'title' => 'Spanish (es)',
            'value' => 'es',
        ]);
        Option::create([
            'category' => 'language',
            'title' => 'Turkish (tr)',
            'value' => 'tr',
        ]);
        Eloquent::reguard();
    }
}
