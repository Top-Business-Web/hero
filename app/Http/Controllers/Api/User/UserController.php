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

    }

    public function getAllCities(): JsonResponse{

        return $this->userRepositoryInterface->getAllCities();
    }
    public function getAllAreas(): JsonResponse{

        return $this->userRepositoryInterface->getAllAreas();
    }

    public function register(Request $request): JsonResponse{

        return $this->userRepositoryInterface->register($request);

    }

    public function login(Request $request): JsonResponse{

        return $this->userRepositoryInterface->login($request);

    }

    public function logout(): JsonResponse
    {

       return $this->userRepositoryInterface->logout();
    }

    public function deleteAccount(): JsonResponse
    {

        return $this->userRepositoryInterface->deleteAccount();
    }

    public function setting(): JsonResponse
    {

        return $this->userRepositoryInterface->setting();
    }


}
