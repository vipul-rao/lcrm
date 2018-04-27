<?php

namespace App\Events\Invoice;

use App\Events\Event;
use App\Models\Invoice;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class InvoiceCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;
    /**
     * @var Invoice
     */
    public $invoice;

    /**
     * Create a new event instance.
     *
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
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
