<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'morning_in',
        'morning_out',
        'afternoon_in',
        'afternoon_out',
        'hours_rendered',
        'status',
        'signature',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Example: Calculate the hours rendered based on check-in/check-out times
    public function calculateHoursRendered()
    {
        // Example calculation logic
        $morningHours = $this->getTimeDifference($this->morning_in, $this->morning_out);
        $afternoonHours = $this->getTimeDifference($this->afternoon_in, $this->afternoon_out);

        // Add both morning and afternoon hours
        $this->hours_rendered = $morningHours + $afternoonHours;
        $this->save();
    }

    // Helper method to get time difference between check-in and check-out
    private function getTimeDifference($checkIn, $checkOut)
    {
        // Assuming check-in and check-out are not null
        if ($checkIn && $checkOut) {
            $checkInTime = \Carbon\Carbon::parse($checkIn);
            $checkOutTime = \Carbon\Carbon::parse($checkOut);
            return $checkInTime->diffInHours($checkOutTime, false);
        }

        return 0;
    }
}
