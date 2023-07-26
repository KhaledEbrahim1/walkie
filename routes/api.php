<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReelController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SearchController;
use Illuminate\Support\Facades\Route;

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

Route::controller(AuthController::class)->group(function (){

    Route::post('register','register');
    Route::post('login','login');
});

Route::middleware('auth:sanctum')->group(function (){

    //auth routes
    Route::post('logout',[AuthController::class,'logout']);

    //posts routes
    Route::apiResource('posts',PostController::class);
    Route::post('post/delete',[PostController::class,'destroy']);
    Route::post('post/update',[PostController::class,'update']);
    Route::post('post/like',[PostController::class,'likePost']);


    //reels routes
    Route::apiResource('reels',ReelController::class);
    Route::post('reel/delete',[ReelController::class,'destroy']);
    Route::post('reel/update',[ReelController::class,'update']);
    Route::post('reel/like',[ReelController::class,'likeReel']);

    //users routes
    Route::controller(UserController::class)->group(function (){
        Route::get('users/{user}', 'show');
        Route::post('users/update', 'update');
        Route::post('users/delete', 'destroy');
        Route::post('users/follow', 'follow');


    });

    //comment routes

    Route::post('post/comment',[CommentController::class,'PostComment']);
    Route::post('reel/comment',[CommentController::class,'ReelComment']);

    // Business routes

    Route::get('Businesses',[BusinessController::class,'index']);
    Route::get('Business/{business}',[BusinessController::class,'show']);

    //review routes

    Route::post('review',[ReviewController::class,'store']);


    // products route

    Route::get('Products',[ProductController::class,'index']);

    //search route

    Route::post('Search',[SearchController::class,'Search']);





});


