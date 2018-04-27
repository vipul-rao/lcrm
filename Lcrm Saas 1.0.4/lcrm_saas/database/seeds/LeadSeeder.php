<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if ('local' === \App::environment()) {
            DB::table('leads')->truncate();

            $users = \App\Models\User::where('id', '>', 1)->get();
            $users->each(function ($user) {
                factory(\App\Models\Lead::class, rand(3, 15))->create(['salesperson_id' => $user->id]);
            });
        } else {
            dd('This is not local environment!');
        }
    }
}
