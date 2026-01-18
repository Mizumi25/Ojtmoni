<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserMapExposureChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public bool $mapExposed;

    public function __construct(int $userId, bool $mapExposed)
    {
        $this->userId = $userId;
        $this->mapExposed = $mapExposed;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('map.exposure.' . $this->userId);
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->userId,
            'map_exposed' => $this->mapExposed,
        ];
    }
}