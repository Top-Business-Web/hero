<?php

namespace App\Providers;

use App\Interfaces\AdminInterface;
use App\Interfaces\Api\Driver\DriverRepositoryInterface;
use App\Interfaces\Api\User\UserRepositoryInterface;
use App\Repository\Api\User\UserRepository as UserApiRepository;
use App\Repository\Api\Driver\DriverRepository as DriverApiRepository;
use App\Interfaces\AreaInterface;
use App\Interfaces\AuthInterface;
use App\Interfaces\CityInterface;
use App\Interfaces\DriverInterface;
use App\Interfaces\SettingInterface;
use App\Interfaces\SliderInterface;
use App\Interfaces\UserInterface;
use App\Repository\AdminRepository;
use App\Repository\AreaRepository;
use App\Repository\AuthRepository;
use App\Repository\CityRepository;
use App\Repository\DriverRepository;
use App\Repository\SettingRepository;
use App\Repository\SliderRepository;
use App\Repository\UserRepository;
use Illuminate\Support\ServiceProvider;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // start Web classes and interfaces
        $this->app->bind(AuthInterface::class,AuthRepository::class);
        $this->app->bind(AdminInterface::class,AdminRepository::class);
        $this->app->bind(AreaInterface::class,AreaRepository::class);
        $this->app->bind(UserInterface::class,UserRepository::class);
        $this->app->bind(DriverInterface::class,DriverRepository::class);
        $this->app->bind(CityInterface::class,CityRepository::class);
        $this->app->bind(SliderInterface::class,SliderRepository::class);
        $this->app->bind(SettingInterface::class,SettingRepository::class);

        // ----------------------------------------------------------------



        // start Api classes and interfaces
        $this->app->bind(UserRepositoryInterface::class,UserApiRepository::class);
        $this->app->bind(DriverRepositoryInterface::class,DriverApiRepository::class);
        // ----------------------------------------------------------------

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
