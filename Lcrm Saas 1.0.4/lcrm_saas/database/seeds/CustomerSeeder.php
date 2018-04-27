<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if ('local' === \App::environment()) {
            //DB::table('customers')->truncate();

            //Delete existing seeded users except the first 4 users
            \App\Models\User::where('id', '>', 4)->get()->each(function ($user) {
                $user->forceDelete();
            });

            $customers = factory(\App\Models\User::class, 20)->create(['user_id' => '2']);
            $customerRole = Sentinel::getRoleRepository()->findByName('customer');

            $customers->each(function ($customer) use ($customerRole) {
                //Attach Customer role
                $customerRole->users()->attach($customer);

                //Add customer data
                $data = factory(\App\Models\Customer::class)->make(['belong_user_id' => 3]);
                $customer->customer()->create($data->toArray());
            });
        } else {
            dd('This is not local environment!');
        }
    }
}
