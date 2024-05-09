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
Route::get('/api/v1/user/activity','Api\GetUserActivityLogController@getUserActivityLog')->name('api.getUserLog');
Route::get('/api/v1/get_attendance','Api\GetAttendanceController@index')->name('api.get.attendance');
Route::get('/api/v1/get/image/{code}','Api\GetAttendanceController@showImage')->name('api.get.image');
// Route::get('/api/v1/get/image/{code}', [GetAttendanceController::class, 'showImage']);
