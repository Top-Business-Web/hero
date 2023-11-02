<?php

namespace App\Interfaces\Api\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface UserRepositoryInterface{
    public function register(Request $request): JsonResponse;
    public function login(Request $request);
    public function logout(): JsonResponse;
    public function deleteAccount(): JsonResponse;
    public function editProfile(Request $request): JsonResponse;
    public function getAllCities(): JsonResponse;
    public function getAllAreas(): JsonResponse;
    public function setting(): JsonResponse;
    public function startTripWithTrack(Request $request): JsonResponse;
    public function cancelTrip(Request $request): JsonResponse;
    public function endTripWithoutTrack(Request $request): JsonResponse;

}
