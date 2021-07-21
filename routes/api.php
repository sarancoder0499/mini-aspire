<?php

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
Route::namespace('App\Http\Controllers')->group(function() {
    Route::post('/register','Auth\RegisterController@registerUser');
    Route::post('/login','Auth\LoginController@loginUser');

    Route::middleware('auth:api')->group(function(){
        Route::post('/loan/all','LoanController@index');
        Route::post('/loan/show','LoanController@show');
        Route::post('/loan/store','LoanController@store');
        Route::post('/loan/approve','LoanController@approve');

        Route::post('/payment/repay','PaymentController@pay');
    });
});
