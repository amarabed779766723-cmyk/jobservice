<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BannedNoteController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\VerifyCodeController;
use App\Http\Controllers\PasswordResetController;

Route::get('/', function () { return redirect('/home'); });

// ========== Routes المصادقة ==========
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ========== Routes العامة ==========
Route::post('/banned-note', [BannedNoteController::class, 'store'])->name('banned.note');
Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/home', [HomeController::class, 'index'])->name('home');

// ========== Routes عرض الخدمات والطلبات والمنشورات (عامة) ==========
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/service/{id}', [ServiceController::class, 'show'])->name('service.show');
Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
Route::get('/request/{id}', [RequestController::class, 'show'])->name('request.show');
Route::get('/post/{id}', [PostController::class, 'show'])->name('post.show');

Route::post('/activate-account', [ProfileController::class, 'activate'])->name('account.activate');

// ========== Routes التحقق من البريد ==========
Route::get('/verify-code', [VerifyCodeController::class, 'showForm'])->name('verify.code.form');
Route::post('/verify-code', [VerifyCodeController::class, 'verify'])->name('verify.code');
Route::post('/verify-code/resend', [VerifyCodeController::class, 'resend'])->name('verify.resend');
Route::get('/verify-code/resend', [VerifyCodeController::class, 'resendForm'])->name('verify.resend.form');

// ========== Routes استعادة كلمة المرور ==========
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendCode'])->name('password.send.code');
Route::get('/reset-code', [PasswordResetController::class, 'showCodeForm'])->name('password.reset.code.form');
Route::post('/reset-code', [PasswordResetController::class, 'verifyCode'])->name('password.verify.code');
Route::post('/reset-code/resend', [PasswordResetController::class, 'resendCode'])->name('password.resend.code');
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.reset');

// ========== Routes حفظ الموقع ==========
Route::post('/save-location', function(Request $request) {
    if (Auth::check()) {
        Auth::user()->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false]);
})->name('save.location');

// ========== Routes الملف الشخصي والإعدادات ==========
Route::get('/settings', function() { return view('settings'); })->name('settings');
Route::post('/settings/theme', [SettingsController::class, 'theme'])->name('settings.theme');

// ========== Routes الاتصال بنا ==========
Route::get('/contact', function() { return view('contact'); })->name('contact');

// ========== Routes تغيير اللغة ==========
Route::get('/switch-locale/{locale}', function ($locale) {
    session()->put('locale', in_array($locale, ['ar', 'en']) ? $locale : 'ar');
    app()->setLocale($locale);
    return redirect('/home');
})->name('switch.locale');

// ========== Routes المحمية (تسجيل دخول) ==========
Route::middleware(['auth', 'check.banned', 'check.active'])->group(function () {
    
    // ===== الملف الشخصي =====
    Route::get('/profile/{id?}', [ProfileController::class, 'show'])->name('profile');
    Route::get('/edit-profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/edit-profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/settings/password', function() { return back()->with('success','تم تغيير كلمة المرور'); })->name('settings.password');
    Route::post('/suspend-account', [ProfileController::class, 'suspend'])->name('account.suspend');
    Route::get('/profile/{user}/followers', [ProfileController::class, 'followers'])->name('profile.followers');
    Route::get('/profile/{user}/following', [ProfileController::class, 'following'])->name('profile.following');

    // ===== الخدمات =====
    Route::get('/create-service', [ServiceController::class, 'create'])->name('service.create');
    Route::post('/create-service', [ServiceController::class, 'store'])->name('service.store');
    Route::delete('/service/{id}', [ServiceController::class, 'destroy'])->name('service.delete');

    // ===== الطلبات =====
    Route::get('/create-request', [RequestController::class, 'create'])->name('request.create');
    Route::post('/create-request', [RequestController::class, 'store'])->name('request.store');
    Route::post('/request/{id}/offer', [RequestController::class, 'submitOffer'])->name('offer.submit');
    Route::post('/offer/{offer}/accept', [RequestController::class, 'acceptOffer'])->name('offer.accept');
    Route::post('/request/{id}/complete', [RequestController::class, 'completeAndRate'])->name('request.complete');

    // ===== المنشورات =====
    Route::get('/create-post', [PostController::class, 'create'])->name('post.create');
    Route::post('/create-post', [PostController::class, 'store'])->name('post.store');
    Route::post('/post/{id}/like', [PostController::class, 'toggleLike'])->name('post.like');
    Route::post('/post/{id}/comment', [PostController::class, 'addComment'])->name('post.comment');
    Route::delete('/post/{id}', [PostController::class, 'delete'])->name('post.delete');

    // ===== الإشعارات =====
    Route::get('/notifications', function() { return view('notifications'); })->name('notifications');

    // ===== الدردشة =====
    Route::get('/messages', [ChatController::class, 'index'])->name('chats.index');
    Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
    Route::get('/chat/start/{user}', [ChatController::class, 'start'])->name('chat.start');
    Route::post('/chat/{id}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::delete('/chat/message/{id}', [ChatController::class, 'deleteMessage'])->name('chat.delete');
    Route::put('/chat/message/{id}', [ChatController::class, 'editMessage'])->name('chat.edit');
    Route::get('/chat/{id}/settings', [ChatController::class, 'settings'])->name('chat.settings');
    Route::post('/chat/{id}/settings', [ChatController::class, 'saveSettings'])->name('chat.settings.save');

    // ===== المتابعات =====
    Route::post('/follow/{user}', [FollowController::class, 'toggle'])->name('follow.toggle');

    // ===== القصص =====
    Route::get('/stories', [StoryController::class, 'index'])->name('stories.index');
    Route::post('/stories', [StoryController::class, 'store'])->name('stories.store');
    Route::get('/stories/user/{user}', [StoryController::class, 'getUserStories'])->name('stories.user');
    Route::post('/stories/{story}/view', [StoryController::class, 'markViewed'])->name('stories.view');
    Route::get('/stories/{story}/views', [StoryController::class, 'getViews'])->name('stories.views');
    Route::delete('/stories/{story}', [StoryController::class, 'delete'])->name('stories.delete');

    // ===== الحجوزات =====
    Route::get('/book/{service_id}', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/book/{service_id}', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/cancel-booking/{id}', [BookingController::class, 'cancel'])->name('booking.cancel');
    Route::post('/booking/{id}/approve', [BookingController::class, 'approve'])->name('booking.approve');
    Route::post('/booking/{id}/reject', [BookingController::class, 'reject'])->name('booking.reject');
    Route::get('/delivery/create/{booking}', [BookingController::class, 'deliveryForm'])->name('delivery.form');
    Route::post('/delivery/send/{booking}', [BookingController::class, 'sendDelivery'])->name('delivery.send');
    Route::post('/delivery/confirm/{id}', [BookingController::class, 'confirmDelivery'])->name('delivery.confirm');
    Route::post('/delivery/rate/{id}', [BookingController::class, 'rateDelivery'])->name('delivery.rate');

    // ===== البلاغات =====
    Route::post('/report', [ReportController::class, 'store'])->name('report.store');

    // ===== إعدادات الخصوصية =====
    Route::post('/settings/privacy', [SettingsController::class, 'privacy'])->name('settings.privacy');
    Route::post('/settings/chat_wallpaper', [SettingsController::class, 'chatWallpaper'])->name('settings.chat_wallpaper');

    // ===== الباقات =====
    Route::get('/packages', function () { return view('packages'); })->name('packages');
    Route::post('/packages/activate', [AuthController::class, 'activatePackage'])->name('packages.activate');

    Route::get('/providers', [App\Http\Controllers\HomeController::class, 'providers'])->name('providers.index');
    
});

// ========== Routes الأدمن ==========
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login']);
    
    Route::post('/password/email', [AdminController::class, 'sendResetCode'])->name('admin.password.email');
    Route::post('/password/verify', [AdminController::class, 'verifyCode'])->name('admin.password.verify');
    Route::post('/password/new', [AdminController::class, 'saveNewPassword'])->name('admin.password.new');

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::match(['get', 'post'], '/users', [AdminController::class, 'users'])->name('admin.users');
        Route::match(['get', 'post'], '/services', [AdminController::class, 'services'])->name('admin.services');
        Route::match(['get', 'post'], '/reports', [AdminController::class, 'reports'])->name('admin.reports');
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
        Route::get('/posts', [AdminController::class, 'posts'])->name('admin.posts');
        Route::post('/posts/delete', [AdminController::class, 'deletePost'])->name('admin.posts.delete');
        Route::post('/posts/approve', [AdminController::class, 'approvePost'])->name('admin.posts.approve');
        Route::get('/posts/approve/{id}', [AdminController::class, 'approvePostByGet'])->name('admin.posts.approve.get');
        Route::get('/notes', [AdminController::class, 'notes'])->name('admin.notes');
        Route::post('/notes/{id}/read', [AdminController::class, 'markNoteRead'])->name('admin.notes.read');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs');
        Route::delete('/activity-logs/{id}', [ActivityLogController::class, 'delete'])->name('admin.activity-logs.delete');
        Route::post('/activity-logs/clear', [ActivityLogController::class, 'clearAll'])->name('admin.activity-logs.clear');
        Route::get('/reports-page', [AdminController::class, 'reportsPage'])->name('admin.reports.page');
        Route::get('/reports-page/export', [AdminController::class, 'exportReports'])->name('admin.reports.export');
        Route::get('/backup', [AdminController::class, 'backupPage'])->name('admin.backup');
        Route::get('/backup/download', [AdminController::class, 'downloadBackup'])->name('admin.backup.download');
        Route::post('/backup/restore', [AdminController::class, 'restoreBackup'])->name('admin.backup.restore');
        Route::get('/notifications', [AdminController::class, 'adminNotifications'])->name('admin.notifications');
        Route::post('/notifications/{id}/read', [AdminController::class, 'markNotifRead'])->name('admin.notifications.read');
        Route::get('/ads', [AdminController::class, 'ads'])->name('admin.ads');
        Route::post('/ads/approve/{id}', [AdminController::class, 'approveAd'])->name('admin.ads.approve');
        Route::post('/ads/reject/{id}', [AdminController::class, 'rejectAd'])->name('admin.ads.reject');
        Route::get('/revenue', [AdminController::class, 'revenue'])->name('admin.revenue');
    });
});