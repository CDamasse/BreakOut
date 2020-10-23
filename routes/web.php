<?php

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
