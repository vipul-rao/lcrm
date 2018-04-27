<?php

namespace App\Events\Quotation;

use App\Events\Event;
use App\Models\QuotationSaleorder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class QuotationCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;
    /**
     * @var QuotationSaleorder
     */
    public $quotationSaleorder;

    /**
     * Create a new event instance.
     *
     * @param QuotationSaleorder $quotationSaleorder
     */
    public function __construct(QuotationSaleorder $quotationSaleorder)
    {
        $this->quotationSaleorder = $quotationSaleorder;
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
