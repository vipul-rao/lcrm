<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LoginTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     */
    public function testPageVisit()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/signin')
                    ->assertSee('Login');
        });
    }
}
