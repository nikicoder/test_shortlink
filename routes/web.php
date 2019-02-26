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

Route::get('/', function () {
    return view('start_page');
});

Route::get('/{path}', 'LinksController@go');
Route::get('/stat/{path}/{secret}', 'LinkStatController@stats');

Route::get('/admin/dashboard', 'DashboardController@linkstats');