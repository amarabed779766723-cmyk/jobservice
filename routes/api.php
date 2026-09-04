<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StoryController;

// ===== الخدمات =====
Route::get('/services/nearby', [ServiceController::class, 'nearby']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::get('/categories', [ServiceController::class, 'categories']);

// ===== الطلبات =====
Route::get('/requests', [RequestController::class, 'index']);
Route::get('/requests/{id}', [RequestController::class, 'show']);

// ===== المنشورات =====
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);

// ===== المستخدمين =====
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);

// ===== القصص والإعلانات =====
Route::get('/stories', [StoryController::class, 'index']);

// ✅ إشعارات
Route::get('/notifications/unread-count', function() {
    $unread = \App\Models\Notification::where('is_read', 0)->count();
    return response()->json(['unread' => $unread]);
});

// ===== المصادقة =====
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ===== Routes محمية =====
Route::middleware('auth:api')->group(function () {
    
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/user', [UserController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
    
    Route::post('/requests', [RequestController::class, 'store']);
    Route::post('/requests/{id}/offer', [RequestController::class, 'submitOffer']);
    
    Route::post('/posts', [PostController::class, 'store']);
    Route::post('/posts/{id}/like', [PostController::class, 'toggleLike']);
    Route::post('/posts/{id}/comment', [PostController::class, 'addComment']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    
    Route::post('/follow/{user}', [UserController::class, 'follow']);
    Route::post('/unfollow/{user}', [UserController::class, 'unfollow']);
});