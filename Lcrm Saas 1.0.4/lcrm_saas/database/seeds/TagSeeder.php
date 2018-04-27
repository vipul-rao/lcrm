<?php

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Eloquent::unguard();

        //truncate existing data
        DB::table('tags')->truncate();

        //driver statuses
        Tag::create([
            'title' => 'Product',
        ]);
        Tag::create([
            'title' => 'Software',
        ]);
        Tag::create([
            'title' => 'Design',
        ]);
        Tag::create([
            'title' => 'Training',
        ]);
        Tag::create([
            'title' => 'Other',
        ]);

        Eloquent::reguard();
    }
}
