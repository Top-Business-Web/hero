<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsuranceDriver extends Model
{
    use HasFactory;

    protected $table = 'insurance_drivers';

    protected $fillable = [
        'driver_id',
        'from',
        'to',
    ];

    public function driver() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
