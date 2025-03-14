<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/greet', function (){
    return view ('Hello World');
});

Route::get('/greeting', function (){
    return ('Hello World');
});

Route::group(['namespace' => 'App\Http\Controllers'], function()
{
Route::get('/', 'PagesController@index')->name('pages.index');
Route::get('/about', 'PagesController@about')->name('pages.about');
Route::get('/visidanmisi', 'PagesController@visidanmisi')->name('pages.visidanmisi');
Route::get('/alumni', 'PagesController@alumni')->name('pages.alumni');
Route::get('/prestasi', 'PagesController@prestasi')->name('pages.prestasi');
});