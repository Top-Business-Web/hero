<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'day',
        'total',
        'vat_total',
    ];
}
