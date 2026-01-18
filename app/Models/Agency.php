<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Location;
use App\Models\Schedule;

class Agency extends Model
{
    /** @use HasFactory<\Database\Factories\AgencyFactory> */
    use HasFactory;

    protected $fillable = [
        'agency_name',
        'agency_background',
        'contact_person_id',
        'agency_number',
        'slot',
        'location_id',
        'agency_radius',
        'agency_image',
    ];

    // Agency.php
    public function contactPerson()
    {
        return $this->belongsTo(User::class, 'contact_person_id')->where('role', 'agency');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function students()
    {
        return $this->hasMany(User::class, 'agency_id')->where('role', 'student');
    }

    public function schedules() // Renamed to plural
    {
        return $this->hasMany(Schedule::class, 'agency_id'); // Foreign key in Schedule model is 'agency_id'
    }
}