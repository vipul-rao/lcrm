<?php

namespace App\Events\EmailTemplate;

use App\Events\Event;
use App\Models\EmailTemplate;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EmailTemplateCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;
    /**
     * @var EmailTemplate
     */
    public $emailTemplate;

    /**
     * Create a new event instance.
     * @param EmailTemplate $emailTemplate
     */
    public function __construct(EmailTemplate $emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
