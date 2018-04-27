<?php

use Illuminate\Database\Seeder;

class SalesTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if ('local' === \App::environment()) {
            DB::table('sales_teams')->truncate();

            factory(\App\Models\Salesteam::class, 5)->create(
                [
                    'user_id' => 3,
                    'team_leader' => 3,
                ]
            );
        } else {
            dd('This is not local environment!');
        }
    }
}
