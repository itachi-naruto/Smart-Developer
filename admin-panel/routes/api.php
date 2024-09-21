<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

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


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:manage_roles');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->middleware('permission:manage_roles');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->middleware('permission:manage_roles');
    Route::post('/users/{id}/assign-role', [RoleController::class, 'assignRoleToUser'])
    ->middleware('permission:manage_roles');
 
    Route::post('/users/{id}/remove-role', [RoleController::class, 'removeRoleFromUser'])
    ->middleware('permission:manage_roles');

    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::post('/permissions', [PermissionController::class, 'store'])->middleware('permission:manage_permissions');
    Route::put('/permissions/{id}', [PermissionController::class, 'update'])->middleware('permission:manage_permissions');
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])->middleware('permission:manage_permissions');
});
