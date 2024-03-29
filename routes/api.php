<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\AuthenticationController;

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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthenticationController::class, 'me']);
    Route::get('/logout', [AuthenticationController::class, 'logout']);


    Route::resource('/products', ProductController::class);
    Route::resource('/orders', OrderController::class);
    Route::post('/checkout/{id}', [OrderController::class, 'checkout']);
    Route::delete('/checkout/{id}', [OrderController::class, 'destroyCheckout']);
});

Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/register', [AuthenticationController::class, 'register']);

Route::post('/login-customer', [AuthenticationController::class, 'loginCustomer']);
Route::post('/register-customer', [AuthenticationController::class, 'registerCustomer']);
