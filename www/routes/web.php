<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return redirect('/thaifood');
})->name('home');

Route::get('/thaifood', 'RestaurantController@thaifood')->name('thai-food');
Route::get('/thaifoodmap', 'RestaurantController@thaiFoodMap');
Route::get('/bestplaces', 'RestaurantController@bestplaces')->name('best-places');
Route::get('/bestplacesmap', 'RestaurantController@bestPlacesMap');
Route::get('/restaurant/{name}', 'RestaurantController@show')->name('show-restaurant-data');
