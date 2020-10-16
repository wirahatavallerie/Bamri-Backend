<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1'], function(){
    Route::group(['prefix' => 'auth'], function(){
        Route::post('/login', 'AuthController@login');
        Route::post('/logout', 'AuthController@logout');
    });
    Route::group(['middleware' => 'auth'], function(){
        Route::group(['prefix' => 'buses'], function(){
            Route::post('/', 'BusController@create');
            Route::get('/', 'BusController@get');
            Route::put('/{bus_id}', 'BusController@update');
            Route::delete('/{bus_id}', 'BusController@delete');
        });
        Route::group(['prefix' => 'drivers'], function(){
            Route::post('/', 'DriverController@create');
            Route::get('/', 'DriverController@get');
            Route::put('/{driver_id}', 'DriverController@update');
            Route::delete('/{driver_id}', 'DriverController@delete');
        });
        Route::group(['prefix' => 'orders'], function(){
            Route::post('/', 'OrderController@create');
            Route::get('/', 'OrderController@get');
            Route::delete('/{order_id}', 'OrderController@delete');
        });
    });
});