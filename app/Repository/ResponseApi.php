<?php

namespace App\Repository;

use Illuminate\Http\JsonResponse;

class ResponseApi{

    public static function returnResponseDataApi($data=null,string $message,int $code,int $status = null): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'code' => $code,

        ],$status = $code);

    }
}
