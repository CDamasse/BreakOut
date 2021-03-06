<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

Route::get('/{route}', function () {
    return view('index');
})->where('route', '.*');

// Pour authentification
Route::prefix('auth')->group(function () {
    Route::post('init', 'AppController@init');
    /* Route::get -> Bug */

    Route::post('login', 'AppController@login');
    Route::post('register', 'AppController@register');
    Route::post('logout', 'AppController@logout');
});

// Admin - Dashboard
Route::prefix('bo_dashboard')->group(function () {
    Route::post('updatecarousel', 'DashboardController@updateCarousel');
    Route::post('loadcarousel', 'DashboardController@loadCarousel');
    Route::post('loadselectedrooms', 'DashboardController@loadSelectedRooms');
    Route::post('loadscrolling', 'DashboardController@loadScrolling');
    Route::post('updatescrolling', 'DashboardController@updateScrolling');
});
// Admin - Users
Route::prefix('users')->group(function () {
    Route::post('loadall', 'UserController@loadAll');
    Route::delete('userdelete/{id}', [UserController::class, 'userDelete']);
    Route::post('findone', [UserController::class, 'findOne']);
    Route::post('searchuser/{search}', [UserController::class, 'searchUser']);
    Route::post('updatestatus', 'UserController@updateStatus');
});

// Admin - Rooms
Route::prefix('bo_rooms')->group(function () {
    Route::post('newroom', 'RoomsController@newRoom');
    Route::post('loadall', 'RoomsController@loadAll');
    Route::post('updateroom', 'RoomsController@updateRoom');
    Route::delete('deleteroom/{id}', 'RoomsController@deleteRoom');
});

// Admin - Items
Route::prefix('bo_items')->group(function () {
    Route::post('loadall', 'ItemsController@loadAll');
    Route::post('newitem', 'ItemsController@newItem');
    Route::post('updateitem', 'ItemsController@updateItem');
    Route::delete('deleteitem/{id}', 'ItemsController@deleteItem');
});

// Shop
Route::prefix('bo_shop')->group(function () {
    Route::post('addgem', 'ShopController@addGem');
});
