<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Agency;
use App\Models\User;
use Malhal\Geographical\Geographical;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory, Geographical;
    
    protected $fillable = [
        'location_name',
        'longitude',
        'latitude',
    ];
    
    protected $casts = [ 
      'latitude' => 'float', 
      'longitude' => 'float',
    ];
    
    public function agency()
    {
        return $this->hasOne(Agency::class);
    }
    
    public function users()
    {
        return $this->hasMany(User::class);
    }

}
