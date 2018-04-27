<?php

namespace App\Events\Product;

use App\Events\Event;
use App\Models\Product;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProductCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;
    /**
     * @var Product
     */
    public $product;

    /**
     * Create a new event instance.
     *
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
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
