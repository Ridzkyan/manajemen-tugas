<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\TugasController;
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
// Tambahkan di api.php
Route::get('/testFlaskConnection', [TugasController::class, 'testFlaskConnection']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
