<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\CustomerController;

use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\Admin\ProductAdminController;

use App\Http\Controllers\Api\Admin\SupplierAdminController;
use App\Http\Controllers\Api\Admin\PurchaseController;
use App\Http\Controllers\Api\Admin\SupplierPaymentController;


Route::post('/login', [AuthController::class, 'login']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/find', [ProductController::class, 'find']);
Route::get("/customers/find", [CustomerController::class, 'find']);
Route::get("/customers/search", [CustomerController::class, 'search']);
Route::get('/sales/{invoice_no}/invoice', [SalesController::class, 'invoice']);
Route::get('/sales/{invoice_no}/print', [SalesController::class, 'print']);

Route::middleware(['auth:sanctum'])->group(function(){

	Route::post('/sales', [SalesController::class, 'store']);
	Route::post('/stock/adjust', [StockController::class, 'adjust']);
	Route::get('/reports/today', [ReportController::class, 'today']);
	Route::get('/reports/top-sales', [ReportController::class, 'topSales']);
	Route::get('/reports/sales', [ReportController::class, 'sales']);

Route::prefix('admin')->group(function(){

	Route::get('/products', [ProductAdminController::class,'index']);
	Route::post('/products', [ProductAdminController::class,'store']);
	Route::get('/products/{product}', [ProductAdminController::class,'show']);
	Route::put('/products/{product}', [ProductAdminController::class,'update']);
	Route::delete('/products/{product}', [ProductAdminController::class,'destroy']);
	Route::get('/suppliers', [SupplierAdminController::class,'index']);
	Route::post('/suppliers', [SupplierAdminController::class,'store']);
	Route::get('/suppliers/{supplier}', [SupplierAdminController::class,'show']);

	Route::match(['post','put'],'/suppliers/{supplier}', [SupplierAdminController::class,'update']);

	Route::delete('/suppliers/{supplier}', [SupplierAdminController::class,'destroy']);

	//Purchases
	Route::get('/purchases/summary', [PurchaseController::class,'summary']);
	Route::get('/purchases', [PurchaseController::class,'index']);
	Route::post('/purchases', [PurchaseController::class,'store']);
	Route::get('/purchases/{purchase}', [PurchaseController::class,'show']);

	//Supplier
	Route::post('/supplier-payments', [SupplierPaymentController::class,'store']);
	});
});
