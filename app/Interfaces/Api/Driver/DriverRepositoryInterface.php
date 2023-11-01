<?php

namespace App\Interfaces\Api\Driver;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface DriverRepositoryInterface{

    public function registerDriver(Request $request): JsonResponse;
    public function registerDriverDoc(Request $request): JsonResponse;
    public function checkDocument(Request $request): JsonResponse;
    public function changeStatus(Request $request): JsonResponse;
    public function updateDriverDetails(Request $request): JsonResponse;
    public function updateDriverDocument(Request $request): JsonResponse;
}
