<?php

use App\Models\PrintTemplate;

class PrintTemplateSeeder extends DatabaseSeeder
{
    public function run()
    {
        Eloquent::unguard();

        //truncate existing data
        DB::table('print_templates')->truncate();
        // invoice template
        PrintTemplate::create([
            'name' => 'Blue',
            'slug' => 'invoice_blue',
            'type' => 'invoice',
        ]);
        PrintTemplate::create([
            'name' => 'Green',
            'slug' => 'invoice_green',
            'type' => 'invoice',
        ]);
        PrintTemplate::create([
            'name' => 'Red-Green',
            'slug' => 'invoice_red_green',
            'type' => 'invoice',
        ]);
        // quotation template
        PrintTemplate::create([
            'name' => 'Blue',
            'slug' => 'quotation_blue',
            'type' => 'quotation',
        ]);
        PrintTemplate::create([
            'name' => 'Green',
            'slug' => 'quotation_green',
            'type' => 'quotation',
        ]);
        PrintTemplate::create([
            'name' => 'Red-Green',
            'slug' => 'quotation_red_green',
            'type' => 'quotation',
        ]);
        // saleorder template
        PrintTemplate::create([
            'name' => 'Blue',
            'slug' => 'saleorder_blue',
            'type' => 'saleorder',
        ]);
        PrintTemplate::create([
            'name' => 'Green',
            'slug' => 'saleorder_green',
            'type' => 'saleorder',
        ]);
        PrintTemplate::create([
            'name' => 'Red-Green',
            'slug' => 'saleorder_red_green',
            'type' => 'saleorder',
        ]);

        Eloquent::reguard();
    }
}
