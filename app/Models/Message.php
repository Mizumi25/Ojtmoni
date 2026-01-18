<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id', 'receiver_id', 'group_id',
        'content', 'media_path', 'type', // 'type' can indicate text, image, video, file
        'shared_announcement_id', 'shared_agency_id',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function group()
    {
        return $this->belongsTo(MessageGroup::class, 'group_id');
    }

    public function sharedAnnouncement()
    {
        return $this->belongsTo(Announcement::class);
    }

    public function sharedAgency()
    {
        return $this->belongsTo(Agency::class);
    }
}