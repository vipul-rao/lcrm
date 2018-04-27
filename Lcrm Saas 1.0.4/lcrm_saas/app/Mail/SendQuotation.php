<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendQuotation extends Mailable
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
            ->markdown('emails.sendQuotation')
            ->attach(url('/pdf/'.$this->args['quotation_pdf']))
            ->with([
                'subject' => $this->args['subject'],
                'message_body' => $this->args['message_body']
            ]);
    }
}
