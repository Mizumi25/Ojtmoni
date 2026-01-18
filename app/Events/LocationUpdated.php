<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $latitude;
    public $longitude;
    public $locationName;

    public function __construct($userId, $latitude, $longitude, $locationName = null)
    {
        $this->userId = $userId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->locationName = $locationName;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('location.' . $this->userId);
    }
    
    public function broadcastWith()
    {
        return [
            'user' => [
                'id' => $this->userId,
                'location' => [
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'location_name' => $this->locationName,
                ],
            ],
        ];
    }
}
