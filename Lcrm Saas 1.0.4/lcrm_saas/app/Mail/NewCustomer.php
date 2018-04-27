<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public $args;

    /**
     * Create a new message instance.
     */
    public function __construct($args)
    {
        $this->args = $args;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->args['from'])
                ->subject($this->args['subject'])
                ->markdown('emails.new_customer')
                ->with([
                        'email' => $this->args['email'],
                        'password' => $this->args['password'],
                        'sitename' => $this->args['sitename'],
                    ]);
    }
}
