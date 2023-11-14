<?php

namespace App\Http\Controllers\Api\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\JsonResponse;


class CheckPhoneController extends Controller{

    public function checkPhone(Request $request): JsonResponse
    {

        $rules = [
            'phone' => 'required|numeric',
        ];

        $user = User::query()
            ->where('phone','=',$request->phone)->first();

        if ($user){
            ResetCodePassword::query()->where('phone', $request->phone)
                ->delete();

            ResetCodePassword::create(['phone' => $request->phone]);
            return self::returnResponseDataApi(null,"The phone is exists",200);
        }else {
            return self::returnResponseDataApi(null,"The phone is not exists",500);

        }

    }

}

