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
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');

Route::match(['get', 'post', 'delete'], '/settings/tokens', Settings\UpdateAccessTokenActionHTML::class)->name('settings_tokens');

Route::get('/page/v1/top', v1\ShowTopActionHTML::class)->name('v1_top');
Route::match(['get', 'post'], '/page/v1/statements', v1\ShowStatementsActionHTML::class)->name('v1_statements');
