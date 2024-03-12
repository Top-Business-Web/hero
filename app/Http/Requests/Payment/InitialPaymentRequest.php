<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class InitialPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "serviceType" => ["required", "string", "max:254"],
            "orderId" => ["required", "string", "max:512"],
        ];
    }
}

