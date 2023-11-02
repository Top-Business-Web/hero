<?php

use App\Http\Controllers\Api\Driver\DriverController;
use App\Http\Controllers\Api\User\CheckPhoneController;
use App\Http\Controllers\Api\User\HomeController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

######################### START AUTH ROUTES ###################################
Route::post('checkPhone', [CheckPhoneController::class, 'checkPhone']);
Route::post('auth/register', [UserController::class, 'register']);
Route::post('auth/login', [UserController::class, 'login']);
########################### END AUTH ROUTES ###################################




######################### START USER ROUTES ###################################
Route::group(['middleware' => 'jwt'],function (){

    Route::get('userHome',[HomeController::class,'home']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('editProfile', [UserController::class, 'editProfile']);
    Route::post('deleteAccount', [UserController::class, 'deleteAccount']);
    Route::get('setting', [UserController::class, 'setting']);

});
########################### END USER ROUTES ###################################




######################### START DRIVER ROUTES #################################
Route::group(['middleware' => 'jwt'],function (){

    Route::post('storeDriverDetails', [DriverController::class, 'registerDriver']);
    Route::post('updateDriverDetails', [DriverController::class, 'updateDriverDetails']);
    Route::post('storeDriverDocument', [DriverController::class, 'registerDriverDoc']);
    Route::post('updateDriverDocument', [DriverController::class, 'updateDriverDocument']);
    Route::post('checkDocument', [DriverController::class, 'checkDocument']);
    Route::post('changeStatus', [DriverController::class, 'changeStatus']);
    #### TRIPS ROUTES ####
    Route::post('startQuickTrip', [DriverController::class, 'startQuickTrip']);
    Route::post('endQuickTrip', [DriverController::class, 'endQuickTrip']);
});
######################### END DRIVER ROUTES ###################################




######################### START GENERAL ROUTES ################################
Route::get('cities', [UserController::class, 'getAllCities']);
Route::get('areas', [UserController::class, 'getAllAreas']);
########################### END GENERAL ROUTES ################################
