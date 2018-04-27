<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Repositories\SettingsRepositoryEloquent;

class SubscriptionExtended extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = (new SettingsRepositoryEloquent(app()))->getKey('site_email');

        return $this->from($email)
        ->subject('Subscription Extended')
        ->markdown('emails.subscriptions.extended');
    }
}
