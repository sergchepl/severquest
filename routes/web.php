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

Route::get('/', 'MainController@index');

Route::post('/send-message', 'TelegramBotController@storeMessage');
Route::get('/send-photo', 'TelegramBotController@sendPhoto');
Route::post('/store-photo', 'TelegramBotController@storePhoto');
Route::get('/updated-activity', 'TelegramBotController@updatedActivity');

Route::put('/take-task', 'MainController@takeTask');
Route::get('/check-tasks', 'MainController@checkTakenTasks');

Auth::routes();
Route::post('/login','Auth\LoginController@authenticate');
