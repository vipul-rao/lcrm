<?php

class AccountSeeder extends DatabaseSeeder
{
    public function run()
    {
        Sentinel::getRoleRepository()->createModel()->create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        Sentinel::getRoleRepository()->createModel()->create([
            'name' => 'User',
            'slug' => 'user',
        ]);

        Sentinel::getRoleRepository()->createModel()->create([
            'name' => 'Staff',
            'slug' => 'staff',
        ]);

        Sentinel::getRoleRepository()->createModel()->create([
            'name' => 'Customer',
            'slug' => 'customer',
        ]);
    }
}
