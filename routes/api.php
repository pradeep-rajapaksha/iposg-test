<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::apiResource('orders', OrderController::class);
Route::get('orders', [OrderController::class, 'index']); // ->middleware('auth:api');
Route::post('orders', [OrderController::class, 'store']); // ->middleware('auth:api');
Route::get('orders/{id}', [OrderController::class, 'show']); // ->middleware('auth:api');
Route::patch('orders/{id}', [OrderController::class, 'update']); // ->middleware('auth:api');
