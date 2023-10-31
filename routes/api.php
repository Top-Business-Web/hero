<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ResetPassword\ForgotPasswordController;
use App\Http\Controllers\Api\ResetPassword\ResetPasswordController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
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

Route::post('checkPhone',  [ForgotPasswordController::class,'checkPhone']);



Route::post('resetPassword', [ResetPasswordController::class,'resetPassword']);


    Route::get('cities',[UserController::class,'getAllCities']);
    Route::post('auth/register',[UserController::class,'register']);
    Route::post('auth/login',[UserController::class,'login']);



    Route::group(['prefix' => 'driver/orders','middleware' => ['jwt','check-auth-type']], function () {

        Route::get('allOrdersOfDriver',[\App\Http\Controllers\Api\Driver\OrderController::class,'allOrdersOfDriver']);
        Route::get('allOrdersCompletedPayment',[\App\Http\Controllers\Api\Driver\OrderController::class,'allOrdersCompletedPayment']);
        Route::get('orderDetail/{id}',[\App\Http\Controllers\Api\Driver\OrderController::class,'orderDetail']);
        Route::post('changeOrderStatus/{id}',[\App\Http\Controllers\Api\Driver\OrderController::class,'changeOrderStatus']);


    });

