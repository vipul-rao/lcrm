<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteStaff extends Mailable
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
                ->markdown('emails.invite')
                ->with([
                        'inviteUrl' => $this->args['inviteUrl'],
                    ]);
    }
}
