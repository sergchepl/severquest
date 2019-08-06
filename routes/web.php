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


Route::get('/', 'MainController@rules');
Route::get('/home', 'MainController@index');

Route::put('/take-task', 'MainController@takeTask');
Route::get('/check-tasks', 'MainController@checkTakenTasks');
Route::post('/send-answer', 'MainController@sendAnswer')->middleware('telegramphoto');
Route::put('/set-score', 'MainController@setScore');

Auth::routes();
Route::post('/login','Auth\LoginController@authenticate');

Route::post('/AAG1RIo_ym-2We-yuTsN8IWg8Jlex7lEY4s/webhook', 'MainController@webhook');
Route::get('/setwebhook', 'TelegramBotController@setWebhook');

Route::group(['middleware' => ['auth', 'admin'], 'prefix' => 'admin'], function() {
    Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');
    Route::get('tasks', 'AdminController@tasks')->name('tasks');
    Route::get('task/create', 'AdminController@createTask')->name('tasks.new');
    Route::get('task/{task}/delete', 'AdminController@deleteTask')->name('task.delete');

    Route::get('users', 'AdminController@users')->name('users');
    Route::get('user/{user}/activate', 'AdminController@activateUser')->name('user.activate');
    Route::get('user/{user}/delete', 'AdminController@deleteUser')->name('user.delete');
});