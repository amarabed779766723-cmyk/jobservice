<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StoryController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ===== Routes العامة (بدون مصادقة) =====
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::get('/requests', [RequestController::class, 'index']);
Route::get('/requests/{id}', [RequestController::class, 'show']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::get('/categories', [ServiceController::class, 'categories']);

// ===== Routes المصادقة =====
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ===== Routes محمية (تتطلب توكن) =====
Route::middleware('auth:api')->group(function () {
    
    // المستخدم
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/user', [UserController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // الخدمات
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
    
    // الطلبات
    Route::post('/requests', [RequestController::class, 'store']);
    Route::post('/requests/{id}/offer', [RequestController::class, 'submitOffer']);
    
    // المنشورات
    Route::post('/posts', [PostController::class, 'store']);
    Route::post('/posts/{id}/like', [PostController::class, 'toggleLike']);
    Route::post('/posts/{id}/comment', [PostController::class, 'addComment']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    
    // المتابعة
    Route::post('/follow/{user}', [UserController::class, 'follow']);
    Route::post('/unfollow/{user}', [UserController::class, 'unfollow']);
    // ===== مستخدمين =====
Route::get('/users', [UserController::class, 'index']);

// ===== قصص/إعلانات =====
Route::get('/stories', [StoryController::class, 'index']);

// ===== التصنيفات =====
Route::get('/categories', [ServiceController::class, 'categories']);
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ===== Routes القديمة (مبقيينها) =====
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// ============================================================
// ===== Routes الجديدة (نضيفها في الآخر) =====
// ============================================================

// ===== خدمات =====
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::get('/categories', [ServiceController::class, 'categories']);

// ===== منشورات =====
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);

// ===== طلبات =====
Route::get('/requests', [RequestController::class, 'index']);
Route::get('/requests/{id}', [RequestController::class, 'show']);

// ===== مستخدمين =====
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);

// ===== قصص وإعلانات =====
Route::get('/stories', [StoryController::class, 'index']);

// ===== مصادقة =====
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/services/nearby', [ServiceController::class, 'nearby']);
