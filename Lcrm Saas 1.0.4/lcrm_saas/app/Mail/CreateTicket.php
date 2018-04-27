<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CreateTicket extends Mailable
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
            ->markdown('emails.create_ticket')
            ->with([
                'userFrom' => $this->args['userFrom'],
                'subject' => $this->args['subject'],
                'message' => $this->args['message'],
                'tickets' => $this->args['tickets']
            ]);
    }
}
