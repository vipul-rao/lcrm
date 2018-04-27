<?php

use App\Models\Customer;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyDataSetup extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        if ('local' !== \App::environment()) {
            return;
        }

        DB::table('sales_teams')->truncate();
        DB::table('opportunities')->truncate();

        //Delete existing seeded users except the first 4 users
        User::where('id', '>', 3)->get()->each(function ($user) {
            $user->forceDelete();
        });

        //Get the default ADMIN
        $admin = User::find(1);
        //$user = User::find(2);
        $userRole = Sentinel::getRoleRepository()->findByName('user');
        $staffRole = Sentinel::getRoleRepository()->findByName('staff');
        $customerRole = Sentinel::getRoleRepository()->findByName('customer');

        //add dummy user, staff and customer
        $user = Sentinel::registerAndActivate([
                'email' => 'user@crm.com',
                'password' => 'user',
                'first_name' => 'User',
                'last_name' => 'Doe',
            ]);
        $user->user_id = $user->id;

        $userRole->users()->save($user);

        $subscription_end_date = now();
        $subscription_end_date->addDays(29);
        /*Subscription::create([
            'user_id'=>$user->id,
            'stripe_active'=>1,
            'ends_at'=>$subscription_end_date,
            'status'=>'Success',
            'payment_method'=>array_rand(array('Cash'=>'Cash','Check'=>'Check','Bank Account'=>'Bank Account','Credit Card'=>'Credit Card')),
            'payment_received'=>random_int(100,5000)
        ]);*/
        $subscription = new Subscription();
        $subscription->user_id = $user->id;
        $subscription->stripe_active = 1;
        $subscription->ends_at = $subscription_end_date;
        $subscription->status = 'Success';
        $subscription->payment_method = array_rand(['Cash' => 'Cash', 'Check' => 'Check', 'Bank Account' => 'Bank Account', 'Credit Card' => 'Credit Card']);
        $subscription->payment_received = random_int(100, 5000);
        $subscription->save();

        $staff = Sentinel::registerAndActivate([
                'email' => 'staff@crm.com',
                'password' => 'staff',
                'first_name' => 'Staff',
                'last_name' => 'Doe',
            ]);
        $staff->user_id = $user->id;
        foreach ($this->getPermissions() as $permission) {
            $staff->addPermission($permission);
        }
        $staffRole->users()->save($staff);

        $customer = Sentinel::registerAndActivate([
                'email' => 'customer@crm.com',
                'password' => 'customer',
                'first_name' => 'Customer',
                'last_name' => 'Doe',
                'user_id' => $user->id,
            ]);
        $customer->user_id = $user->id;
//            Customer::create(array('user_id' => $customer->id, 'belong_user_id' => $staff->id));
        $customerRole->users()->save($customer);

        //add respective roles

//            $staffRole = Sentinel::findRoleById(2);
//            $staffRole->users()->attach($staff);
//            $customerRole = Sentinel::findRoleById(3);
//            $customerRole->users()->attach($customer);

        //Seed Sales teams for default ADMIN
//            foreach (range(1, 2) as $j) {
//                $this->createSalesTeam($admin->id, $j, $admin);
//                $this->createOpportunity($admin, $admin->id, $j);
//            }
//
//            //Get the default STAFF
//            $staff = User::find(2);
//            $this->createSalesTeam($staff->id, 1, $staff);
//            $this->createSalesTeam($staff->id, 2, $staff);

        //Seed Sales teams for each STAFF
        foreach (range(1, 2) as $j) {
            $this->createSalesTeam($staff->id, $j, $staff);
            $this->createOpportunity($staff, $staff->id, $j);
        }

//            foreach (range(1, 2) as $i) {
//                $staff = $this->createStaff($i);
//                $admin->users()->save($staff);
//                $staffRole->users()->attach($staff);
//
//                $customer = $this->createCustomer($i);
//                $staff->users()->save($customer);
//                $customerRole->users()->attach($customer);
//                $customer->customer()->save(factory(\App\Models\Customer::class)->make());
//
//
//                //Seed Sales teams for each STAFF
//                foreach (range(1, 2) as $j) {
//                    $this->createSalesTeam($staff->id, $j, $staff);
//                    $this->createOpportunity($staff, $i, $j);
//                }
//
//            }
    }

    /**
     * @param $i
     * @param $j
     * @param $staff
     */
    private function createSalesTeam($i, $j, $staff)
    {
        $salesTeam = factory(\App\Models\Salesteam::class)->make([
            'salesteam' => 'STeam - '.$i.' - '.$j,
            'team_leader' => $staff->id,
        ]);

        return $staff->salesteams()->save($salesTeam);
    }

    /**
     * @param $i
     *
     * @return mixed
     */
    private function createStaff($i)
    {
        $staff = Sentinel::registerAndActivate([
            'email' => 'staff'.$i.'@crm.com',
            'password' => 'staff',
            'first_name' => 'Staff',
            'last_name' => $this->convertNumberToWord($i),
        ]);

        return $staff;
    }

    /**
     * @param $staff
     * @param $i
     * @param $j
     */
    private function createOpportunity($staff, $i, $j)
    {
        $opprtunity = $staff->opportunities()->save(factory(\App\Models\Opportunity::class)->make([
            'opportunity' => 'Opp '.$i.' - '.$j,
            'stages' => array_rand($this->stages()),
        ]));

        return $opprtunity;
    }

    private function stages()
    {
        return [
            'New' => 'New',
            'Qualification' => 'Qualification',
            'Proposition' => 'Proposition',
            'Negotiation' => 'Negotiation',
            'Won' => 'Won',
            'Lost' => 'Lost',
            'Dead' => 'Dead',
        ];
    }

    private function convertNumberToWord($num = false)
    {
        $num = str_replace([',', ' '], '', trim($num));
        if (!$num) {
            return false;
        }
        $num = (int) $num;
        $words = [];
        $list1 = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
            'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen',
        ];
        $list2 = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred'];
        $list3 = ['', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
            'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
            'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion',
        ];
        $num_length = strlen($num);
        $levels = (int) (($num_length + 2) / 3);
        $max_length = $levels * 3;
        $num = substr('00'.$num, -$max_length);
        $num_levels = str_split($num, 3);
        for ($i = 0; $i < count($num_levels); ++$i) {
            --$levels;
            $hundreds = (int) ($num_levels[$i] / 100);
            $hundreds = ($hundreds ? ' '.$list1[$hundreds].' hundred'.(1 == $hundreds ? '' : 's').' ' : '');
            $tens = (int) ($num_levels[$i] % 100);
            $singles = '';
            if ($tens < 20) {
                $tens = ($tens ? ' '.$list1[$tens].' ' : '');
            } else {
                $tens = (int) ($tens / 10);
                $tens = ' '.$list2[$tens].' ';
                $singles = (int) ($num_levels[$i] % 10);
                $singles = ' '.$list1[$singles].' ';
            }
            $words[] = $hundreds.$tens.$singles.(($levels && (int) ($num_levels[$i])) ? ' '.$list3[$levels].' ' : '');
        } //end for loop
        $commas = count($words);
        if ($commas > 1) {
            $commas = $commas - 1;
        }

        return implode(' ', $words);
    }

    /**
     * @return mixed
     */
    private function createCustomer($i)
    {
        //$customer = factory(User::class)->make();
        $customer = Sentinel::registerAndActivate([
            'email' => 'customer'.$i.'@crm.com',
            'password' => 'customer',
            'first_name' => 'Customer',
            'last_name' => $this->convertNumberToWord($i),
        ]);

        return $customer;
    }
}
