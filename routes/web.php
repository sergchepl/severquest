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

Auth::routes();
Route::post('/login', 'Auth\LoginController@authenticate');

// Client routes
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'MainController@rules');
    Route::get('/game', 'MainController@index');
    Route::put('/task/{task}/take', 'MainController@takeTask');
    Route::put('/task/{task}/cancel', 'MainController@cancelTask');
    Route::post('/task/{task}/check', 'MainController@checkTask');
    Route::put('/set-score', 'MainController@setScore');
});

// Telegram routes
Route::get('/setwebhook', 'TelegramController@setWebhook');
Route::post('/AAG1RIo_ym-2We-yuTsN8IWg8Jlex7lEY4s/webhook', 'TelegramController@webhook');

// Admin routes
Route::group(['middleware' => ['auth', 'admin'], 'prefix' => 'admin'], function () {
    Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');
    Route::get('tasks', 'AdminController@tasks')->name('tasks');
    Route::get('task/create', 'AdminController@createTask')->name('task.new');
    Route::get('task/{task}/edit', 'AdminController@editTask')->name('task.edit');
    Route::post('task/save', 'AdminController@saveTask')->name('task.save');
    Route::get('task/{task}/clear', 'AdminController@clearTask')->name('task.clear');
    Route::post('task/{task}/update', 'AdminController@updateTask')->name('task.update');
    Route::get('task/{task}/delete', 'AdminController@deleteTask')->name('task.delete');
    Route::get('bans', 'AdminController@bans')->name('bans');
    Route::get('ban/{ban}/delete', 'AdminController@deleteBan')->name('ban.delete');

    Route::get('users', 'AdminController@users')->name('users');
    Route::get('user/{user}/clear', 'AdminController@clearUser')->name('user.clear');
    Route::get('user/{user}/activate', 'AdminController@activateUser')->name('user.activate');
    Route::get('user/{user}/delete', 'AdminController@deleteUser')->name('user.delete');
});
