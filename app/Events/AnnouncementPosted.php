<?php

namespace App\Events;

use App\Models\Announcement;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class AnnouncementPosted implements ShouldBroadcast
{
    use SerializesModels;

    public $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement->load('user', 'images');
    }

    public function broadcastOn(): Channel
    {
        return new Channel('noticeboard');
    }

    public function broadcastAs(): string
    {
        return 'announcement.posted';
    }
}
