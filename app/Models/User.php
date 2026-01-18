<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Course;
use App\Models\YearLevel;
use App\Models\Location;
use App\Models\Agency;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_id',
        'course_id',
        'year_level_id',
        'course_offering_id',
        'school_id_image',
        'phone_number',
        'status',
        'location_id',
        'agency_id',
        'map_exposed',
        'profile_picture',
        'remaining_hours',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function yearLevel()
    {
        return $this->belongsTo(YearLevel::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function dailyLogs()
    {
        return $this->hasMany(DailyLog::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(MessageGroup::class, 'group_user');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
    
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
    
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function hasRole($roles)
    {
        return in_array($this->role, (array) $roles);
    }

    public function isCoordinator()
    {
        return $this->role === 'coordinator';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'map_exposed' => 'boolean',
        ];
    }
}