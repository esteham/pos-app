<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;

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

Route::post('/login', [AuthController::class, 'login']);

//Protected routes (only for token users)
Route::middleware(['auth:sanctum'])->group(function (){
    Route::get('/user', function (Request $request){
        return $request->user();
    });

    Route::post('/sales', [SaleController::class, 'store']);

    Route::post('/logout', [AuthController::class, 'logout']);

});

Route::match(['get', 'post'], '/products/search', [ProductController::class, 'search']);
Route::match(['get', 'post'], '/products/find', [ProductController::class, 'find']);
