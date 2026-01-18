<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_name',
        'agency_background',
        'description',
        'location_id',
        'requested_by_user_id',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }
}