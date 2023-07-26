<?php

use App\Http\Controllers\web\ProductController;
use App\Http\Controllers\Web\adminController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:admin')->group(function () {

//    Route::get('/', function () { return view('dashboard.index');})->name('/');
    Route::get('/',[UserController::class,'reels']);
    Route::get('Users',[UserController::class,'users'])->name('get-users');
    Route::get('Logout',[adminController::class,'destroy'])->name('admin.logout');

    //product
    Route::get('Add-product',[adminController::class,'product'])->name('admin.product');
    Route::post('Add-product',[adminController::class,'add_product'])->name('admin.product.stroe');

    //business

    Route::get('Add-business',[adminController::class,'business'])->name('admin.business');
    Route::post('Add-business',[adminController::class,'add_business'])->name('admin.business.stroe');

});

Route::get('Login',[adminController::class,'login'])->name('login');
Route::post('Login',[adminController::class,'storelogin'])->name('admin.storelogin');
