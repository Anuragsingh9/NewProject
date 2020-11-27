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
Route::post('register', 'AuthController@register'); // User registration
Route::post('login', 'AuthController@login'); // login

Route::middleware('auth:api')->group(function() {

//    Route::get('user/{userId}/detail', 'UserController@show');
    Route::get('logout', 'AuthController@logout'); // logout
    Route::post('create/task','TodoController@createTask'); // create task
    Route::post('update/task/{taskId}','TodoController@updateTasks'); // update task
    Route::get('get/task','TodoController@getTasksByUser'); // get all task of logged in user
    Route::get('all/task','TodoController@getAllTasks'); // get all task(irrespective of user)
    Route::post('delete','TodoController@deleteTask'); // delete a task
    Route::get('task','TodoController@getATask'); // get a particular task
    Route::get('search','TodoController@searchTask'); // search task of logged in user
    Route::post('task/timing','TodoController@setTaskTiming'); // schedule a date for task
    Route::get('today/task','TodoController@getTodaysTasks'); // get all task which have schedule date of today of logged in user
    Route::get('today/count','TodoController@todayTaskCount'); // count all task of today of logged in user
    Route::get('sevenDays/task','TodoController@getNextSevenDaysTasks'); // get all task which have schedule date of next seven days of logged in user
    Route::get('sevenDays/count','TodoController@countSevenDaysTasks'); // count all task of next seven days of logged in user
    Route::get('count/task','TodoController@countTask'); // count task in fraction of completed and incomplete task
    Route::post('update/date','TodoController@updateTaskTiming'); //update a schedule date
//    Route::get('particular-date/task','TodoController@getParticularDateTask'); //get all the task of a given  date

});