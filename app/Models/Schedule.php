<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    // Define fillable fields
    protected $fillable = [
        'user_id',
        'agency_id', // Ensure agency_id is fillable
        'day_of_week',
        'expected_morning_in',
        'expected_morning_out',
        'expected_afternoon_in',
        'expected_afternoon_out',
        'late_tolerance',
        'grace_period',
        'overtime_allowed',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class); // Each schedule belongs to one user, the one created the sched
    }

    public function agency() // Add this relationship
    {
        return $this->belongsTo(Agency::class); // Each schedule belongs to one agency
    }

    /**
     * Get the full checkout time including overtime, if allowed.
     *
     * @param  string  $shift (morning or afternoon)
     * @return string
     */
    public function getCheckoutTimeIncludingOvertime($shift)
    {
        $checkoutField = "expected_{$shift}_out";
        $overtimeField = 'overtime_allowed';

        // Get the expected checkout time
        $checkoutTime = $this->{$checkoutField};

        // If overtime is allowed, extend the checkout time
        if ($this->{$overtimeField}) {
            // Convert the overtime to minutes and add to the checkout time
            $overtimeDuration = $this->parseOvertime($this->{$overtimeField});
            $checkoutTime = $this->addMinutesToTime($checkoutTime, $overtimeDuration);
        }

        return $checkoutTime;
    }

    /**
     * Convert overtime time (e.g., '02:00') to minutes.
     *
     * @param  string  $overtimeTime
     * @return int
     */
    private function parseOvertime($overtimeTime)
    {
        // Split the time into hours and minutes
        list($hours, $minutes) = explode(':', $overtimeTime);

        // Convert to minutes and return
        return ($hours * 60) + $minutes;
    }

    /**
     * Add minutes to a given time.
     *
     * @param  string  $time
     * @param  int  $minutes
     * @return string
     */
    private function addMinutesToTime($time, $minutes)
    {
        // Create a Carbon instance from the time
        $timeInstance = \Carbon\Carbon::parse($time);

        // Add the minutes to the time
        $newTime = $timeInstance->addMinutes($minutes);

        // Return the new time in 'H:i' format
        return $newTime->format('H:i');
    }
}