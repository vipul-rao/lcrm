<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Model::unguard();

        $this->call('AccountSeeder');
        $this->call('CountrySeeder');
        $this->call('StateSeeder');
        $this->call('CitySeeder');
        $this->call('OptionSeeder');
        $this->call('TagSeeder');
        $this->call('PrintTemplateSeeder');
        $this->call('SettingsSeeder');
        $this->call('LanguageSeeder');

        Model::reguard();
    }
}
