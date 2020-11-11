<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');

Route::middleware('auth:api')->group(function() {

    Route::get('user/{userId}/detail', 'UserController@show');
    Route::get('logout', 'AuthController@logout');
    Route::post('create/task','TodoController@createTask');
    Route::post('update/task','TodoController@updateTasks');
    Route::get('get/task','TodoController@getTasksByUser');
    Route::get('all/task','TodoController@getAllTasks');
    Route::post('delete','TodoController@deleteTask');
    Route::get('task','TodoController@getATask');
    Route::get('search','TodoController@searchTask');
    Route::post('task/timing','TodoController@setTaskTiming');
    Route::get('today/task','TodoController@getTodaysTasks');
    Route::get('today/count','TodoController@todayTaskCount');
    Route::get('sevenDays/task','TodoController@getNextSevenDaysTasks');
    Route::get('sevenDays/count','TodoController@countSevenDaysTasks');
    Route::get('count/task','TodoController@countTask');







});