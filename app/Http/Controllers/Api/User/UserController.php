<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Interfaces\Api\User\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller{


    public UserRepositoryInterface $userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {

        $this->userRepositoryInterface = $userRepositoryInterface;

    } // constructor

    public function getAllCities(): JsonResponse
    {
        return $this->userRepositoryInterface->getAllCities();
    } // getAllCities

    public function getAllAreas(): JsonResponse
    {
        return $this->userRepositoryInterface->getAllAreas();
    } // getAllAreas

    public function register(Request $request): JsonResponse
    {
        return $this->userRepositoryInterface->register($request);
    } // register

    public function login(Request $request): JsonResponse
    {
        return $this->userRepositoryInterface->login($request);
    } // login

    public function logout(): JsonResponse
    {
       return $this->userRepositoryInterface->logout();
    } // logout
    public function deleteAccount(): JsonResponse
    {
        return $this->userRepositoryInterface->deleteAccount();
    } // deleteAccount

    public function setting(): JsonResponse
    {
        return $this->userRepositoryInterface->setting();
    } // setting
    public function editProfile(Request $request) : JsonResponse
    {
        return $this->userRepositoryInterface->editProfile($request);
    } // editProfile
    public function startTrip(Request $request) : JsonResponse
    {
        return $this->userRepositoryInterface->startTrip($request);
    } // startTripWithTrack

    public function cancelTrip(Request $request) : JsonResponse
    {
        return $this->userRepositoryInterface->cancelTrip($request);
    } // cancelTrip
}
