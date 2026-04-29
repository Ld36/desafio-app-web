<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;

// ROTAS PÚBLICAS
// Login
Route::post('/login', [AuthController::class, 'login']);
Route::post('/users', [UserController::class, 'store']);


// ROTAS PROTEGIDAS (Precisam do Token do Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    // CRUD de Usuários
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::apiResource('products', ProductController::class);
    Route::get('/relatorio-sql', [ReportController::class, 'relatorioSql']);
});