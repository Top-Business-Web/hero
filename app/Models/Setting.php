<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'trip_insurance',
        'rewards',
        'about',
        'support',
        'safety_roles',
        'polices',
        'km',
        'vat'
    ];
}
