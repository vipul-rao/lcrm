<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaypalSubscriptionCreated extends Mailable
{
    use Queueable, SerializesModels;
    private $args;
    /**
     * Create a new message instance.
     *
     * @return void
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
            ->markdown('emails.subscriptions.paypal_subscription_created')
            ->with([
                'subject' => $this->args['subject'],
                'subscription' => $this->args['subscription']
            ]);
    }
}
