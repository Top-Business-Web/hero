<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsurancePayment extends Model
{
    use HasFactory;

    protected $table = 'insurance_payments';

    protected $fillable = [
        'insurance_driver_id',
        'trans_action_id',
        'type',
        'amount',
        'status',
    ];


    public function insuranceDriver() : BelongsTo
    {
        return $this->belongsTo(InsuranceDriver::class);
    }
}
