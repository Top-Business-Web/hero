<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'trip_type',
        'from_address',
        'from_long',
        'from_lat',
        'to_address',
        'to_long',
        'to_lat',
        'time_ride',
        'time_arrive',
        'distance',
        'time',
        'price',
        'name',
        'phone',
        'user_id',
        'driver_id',
        'ended'
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class,'driver_id','id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
