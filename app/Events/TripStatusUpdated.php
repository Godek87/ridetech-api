<?php
declare(strict_types=1);

namespace App\Events;

use App\Domain\Trip\Entities\Trip;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripStatusUpdated implements \Illuminate\Contracts\Broadcasting\ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trip;

    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }

    public function broadcastOn()
    {
        return new Channel('trip.' . $this->trip->id);
    }

    public function broadcastWith()
    {
        return [
            'status' => $this->trip->status,
            'trip_id' => $this->trip->id,
            'message' => 'Trip status updated',
        ];
    }
}
