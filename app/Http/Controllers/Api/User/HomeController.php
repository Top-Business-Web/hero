<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Interfaces\Api\User\UserRepositoryInterface;

class HomeController extends Controller
{
    public UserRepositoryInterface $userRepositoryInterface;

    /**
     * @param UserRepositoryInterface $userRepositoryInterface
     */
    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {

        $this->userRepositoryInterface = $userRepositoryInterface;

    }

    public function home()
    {
        return $this->userRepositoryInterface->userHome();
    }
}
