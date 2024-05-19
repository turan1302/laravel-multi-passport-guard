<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'auth'],function (){
    Route::post('register',[\App\Http\Controllers\api\auth\indexController::class,'register']);
    Route::post('login',[\App\Http\Controllers\api\auth\indexController::class,'login']);

    Route::group(['middleware'=>'auth:api'],function (){
        Route::get('profile',[\App\Http\Controllers\api\auth\indexController::class,'profile']);
        Route::post('update',[\App\Http\Controllers\api\auth\indexController::class,'update']);
        Route::get('check',[\App\Http\Controllers\api\auth\indexController::class,'check']);
        Route::get('logout',[\App\Http\Controllers\api\auth\indexController::class,'logout']);
    });
});

Route::group(['prefix'=>'auth/user'],function (){
    Route::post('register',[\App\Http\Controllers\api\auth\userController::class,'register']);
    Route::post('login',[\App\Http\Controllers\api\auth\userController::class,'login']);

    Route::group(['middleware'=>'auth:user'],function (){
        Route::get('profile',[\App\Http\Controllers\api\auth\userController::class,'profile']);
        Route::post('update',[\App\Http\Controllers\api\auth\userController::class,'update']);
        Route::get('check',[\App\Http\Controllers\api\auth\userController::class,'check']);
        Route::get('logout',[\App\Http\Controllers\api\auth\userController::class,'logout']);
    });
});
