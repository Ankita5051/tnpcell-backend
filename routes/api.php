<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\TprController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/forget-password',[UserController::class,'forgetPassword']); ///outside of middeware because no need of token pass in this route

Route::group(['middleware'=>'api'],function($routes){
    Route::post('/register',[UserController::class,'register']);
    Route::post('/login',[UserController::class,'login']);
    Route::get('/logout',[UserController::class,'logout']);
    Route::get('/profile',[UserController::class,'profile']);
    Route::post('/profile-update',[UserController::class,'updateProfile']);
    Route::get('/verification-mail/{email}',[UserController::class,'sendVerificatioMail']);
    Route::get('/refresh-token',[UserController::class,'refreshToken']);

    ///apis for job application

    //student
    Route::post('/student-register',[StudentController::class,'register']);
    Route::post('/student-login',[StudentController::class,'login']);
    Route::get('/user/logout',[StudentController::class,'logout']);
    Route::post('/student/reset-password',[StudentController::class,'forgetPassword']);
    Route::get('/student/profile',[StudentController::class,'profile']);
//admin
Route::post('/admin/reset-password',[AdminController::class,'forgetPassword']);
//Tpr
Route::post('/tpr/reset-password',[TprController::class,'forgetPassword']);
});