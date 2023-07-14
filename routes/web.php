<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\TprController;
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
Route::get('/account-verification/{token}',[UserController::class,'verificationMail']);
// Route::get('/reset-password',[UserController::class,'resetPasswordLoad']);
// Route::post('/reset-password',[UserController::class,'resetPassword']);

Route::get('/reset-password',[StudentController::class,'resetPasswordLoad']);
Route::post('/reset-password',[StudentController::class,'resetPassword']);
//for admin
Route::get('/admin/reset-password',[AdminController::class,'resetPasswordLoad']);
Route::post('/admin/reset-password',[AdminController::class,'resetPassword']);
//for tpr
Route::get('/tpr/reset-password',[TprController::class,'resetPasswordLoad']);
Route::post('/tpr/reset-password',[TprController::class,'resetPassword']);