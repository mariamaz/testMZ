<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/app', function () {
    return view('welcome');
});

Auth::routes();


Route::get('/', 'HomeController@index')->name('home');
Route::get('/list', 'HomeController@list');
Route::get('/like', 'HomeController@like');
Route::get('/delete', 'HomeController@delete');

Route::get('list/{type}', 'HomeController@list');
Route::get('/prefered', 'HomeController@index')
Route::get('/nearby', 'HomeController@index')

