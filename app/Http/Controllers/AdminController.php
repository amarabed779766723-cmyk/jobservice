<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Service;
use App\Models\Report;
use App\Models\AdminLog;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Post;
use App\Models\BannedNote;
use App\Models\Story;
use Illuminate\Support\Facades\Auth;
    

class AdminController extends Controller
{
    public function showLogin() { return view('admin.login'); }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials) && Auth::guard('admin')->user()->role === 'admin') {
            ActivityLogController::log(Auth::guard('admin')->id(), 'admin_login', 'تسجيل دخول الأدمن');
            return redirect('/admin/dashboard');
        }
        return back()->withErrors(['email' => 'بيانات غير صحيحة أو ليست لديك صلاحية']);
    }

    public function dashboard()
    {
        $usersCount = User::count();
        $servicesCount = Service::count();
        $reportsCount = Report::where('status', 'pending')->count();
        $postsCount = Post::count();
        $chatsCount = Chat::count();
        $bannedCount = User::where('status', 'banned')->count();
        $providersCount = User::where('user_type', 'provider')->count();
        $clientsCount = User::where('user_type', 'client')->count();
        
        $todayUsers = User::whereDate('created_at', today())->count();
        $todayPosts = Post::whereDate('created_at', today())->count();
        $totalRatings = \App\Models\Rating::count();
        $totalOffers = \App\Models\Offer::count();
        $totalRequests = \App\Models\ServiceRequest::count();
        $totalStories = \App\Models\Story::count();
        $totalActivityLogs = \App\Models\ActivityLog::count();
        
        $latestUsers = User::latest()->take(5)->get();
        $latestActivities = \App\Models\ActivityLog::with('user')->latest()->take(10)->get();

        $totalRevenue = \App\Models\UserPackage::where('price_paid', '>', 0)->sum('price_paid')
                      + \App\Models\Story::where('is_ad', 1)->where('status', 'approved')->sum('ad_price')
                      + \App\Models\Offer::where('status', 'accepted')->sum('price');

        $todayRevenue = \App\Models\UserPackage::where('price_paid', '>', 0)->whereDate('created_at', today())->sum('price_paid')
                      + \App\Models\Story::where('is_ad', 1)->where('status', 'approved')->whereDate('created_at', today())->sum('ad_price')
                      + \App\Models\Offer::where('status', 'accepted')->whereDate('created_at', today())->sum('price');
        
        return view('admin.dashboard', compact(
            'usersCount', 'servicesCount', 'reportsCount', 'postsCount',
            'chatsCount', 'bannedCount', 'providersCount', 'clientsCount',
            'todayUsers', 'todayPosts', 'totalRatings', 'totalOffers',
            'totalRequests', 'totalStories', 'totalActivityLogs',
            'latestUsers', 'latestActivities',
            'totalRevenue', 'todayRevenue'
        ));
    }

    public function users()
    {
        if (request()->isMethod('post')) {
            $user = User::find(request('user_id'));
            $newStatus = request('action') === 'ban' ? 'banned' : 'active';
            $user->update(['status' => $newStatus]);
            AdminLog::create(['admin_id' => Auth::guard('admin')->id(), 'action' => request('action'), 'target_type' => 'user', 'target_id' => $user->id]);
            ActivityLogController::log(Auth::guard('admin')->id(), request('action'), (request('action') === 'ban' ? 'حظر' : 'فك حظر') . ' مستخدم: ' . $user->name);
            return back();
        }
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function services()
    {
        if (request()->isMethod('post')) {
            $service = Service::find(request('service_id'));
            AdminLog::create(['admin_id' => Auth::guard('admin')->id(), 'action' => 'delete', 'target_type' => 'service', 'target_id' => $service->id, 'details' => 'حذف خدمة: ' . $service->title]);
            ActivityLogController::log(Auth::guard('admin')->id(), 'delete_service', 'حذف خدمة: ' . $service->title);
            $service->delete();
            return back();
        }
        $services = Service::with('provider')->orderBy('created_at', 'desc')->get();
        return view('admin.services', compact('services'));
    }

    public function reports()
    {
        if (request()->isMethod('post')) {
            $report = Report::find(request('report_id'));
            if (request('action') === 'delete_content' && request('service_id')) {
                Service::destroy(request('service_id'));
                $report->update(['status' => 'resolved']);
            } else {
                $report->update(['status' => request('action') === 'reviewed' ? 'reviewed' : 'resolved']);
            }
            AdminLog::create(['admin_id' => Auth::guard('admin')->id(), 'action' => 'review_report', 'target_type' => 'report', 'target_id' => $report->id]);
            ActivityLogController::log(Auth::guard('admin')->id(), 'review_report', 'مراجعة بلاغ');
            return back();
        }
        $reports = Report::with(['reporter', 'reported', 'service'])->orderBy('created_at', 'desc')->get();
        return view('admin.reports', compact('reports'));
    }

    public function logout()
    {
        ActivityLogController::log(Auth::guard('admin')->id(), 'admin_logout', 'تسجيل خروج الأدمن');
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    }

    public function posts()
    {
        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.posts', compact('posts'));
    }
    
    public function deletePost(Request $request)
    {
        $post = Post::findOrFail($request->post_id);
        $post->delete();
        AdminLog::create(['admin_id' => Auth::guard('admin')->id(), 'action' => 'delete_post', 'target_type' => 'post', 'target_id' => $post->id, 'details' => 'حذف منشور']);
        ActivityLogController::log(Auth::guard('admin')->id(), 'delete_post', 'حذف منشور (أدمن)');
        return back()->with('success', 'تم حذف المنشور');
    }

    public function approvePost(Request $request)
    {
        $post = Post::findOrFail($request->post_id);
        $post->update(['is_approved' => 1]);
        
        \App\Models\Notification::create([
            'user_id' => $post->user_id,
            'sender_id' => Auth::guard('admin')->id(),
            'type' => 'post_approved',
            'message' => '✅ تم قبول منشورك',
            'link' => route('post.show', $post->id)
        ]);
        
        ActivityLogController::log(Auth::guard('admin')->id(), 'approve_post', 'الموافقة على منشور #' . $post->id);
        
        return back()->with('success', 'تم قبول المنشور وإشعار المستخدم');
    }

    public function notes()
    {
        $notes = BannedNote::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.notes', compact('notes'));
    }

    public function markNoteRead($id)
    {
        $note = BannedNote::findOrFail($id);
        $note->update(['is_read' => 1]);
        return back();
    }

    public function statistics()
    {
        $totalUsers = User::count();
        $totalProviders = User::where('user_type', 'provider')->count();
        $totalClients = User::where('user_type', 'client')->count();
        $totalServices = Service::count();
        $totalRequests = \App\Models\ServiceRequest::count();
        $totalOffers = \App\Models\Offer::count();
        $totalRatings = \App\Models\Rating::count();
        $totalPosts = Post::count();
        $totalStories = \App\Models\Story::count();
        $totalChats = Chat::count();
        $totalBookings = \App\Models\Booking::count();
        $totalReports = Report::count();
        $totalBanned = User::where('status', 'banned')->count();
        $avgRating = round(\App\Models\Rating::avg('score'), 1);
        $totalRevenue = \App\Models\Offer::where('status', 'accepted')->sum('price');
        
        $monthUsers = User::whereMonth('created_at', now()->month)->count();
        $monthServices = Service::whereMonth('created_at', now()->month)->count();
        $monthRequests = \App\Models\ServiceRequest::whereMonth('created_at', now()->month)->count();
        $monthPosts = Post::whereMonth('created_at', now()->month)->count();
        $monthRevenue = \App\Models\Offer::where('status', 'accepted')->whereMonth('created_at', now()->month)->sum('price');
        
        $usersByType = [
            'مقدم خدمة' => $totalProviders,
            'باحث' => $totalClients,
        ];
        
        $requestsByStatus = [
            'مفتوح' => \App\Models\ServiceRequest::where('status', 'open')->count(),
            'قيد التنفيذ' => \App\Models\ServiceRequest::where('status', 'in_progress')->count(),
            'مكتمل' => \App\Models\ServiceRequest::where('status', 'completed')->count(),
            'ملغي' => \App\Models\ServiceRequest::where('status', 'cancelled')->count(),
        ];
        
        return view('admin.statistics', compact(
            'totalUsers', 'totalProviders', 'totalClients', 'totalServices',
            'totalRequests', 'totalOffers', 'totalRatings', 'totalPosts',
            'totalStories', 'totalChats', 'totalBookings', 'totalReports',
            'totalBanned', 'avgRating', 'totalRevenue',
            'monthUsers', 'monthServices', 'monthRequests', 'monthPosts', 'monthRevenue',
            'usersByType', 'requestsByStatus'
        ));
    }

    public function reportsPage()
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[now()->subMonths($i)->format('Y-m')] = now()->subMonths($i)->format('F Y');
        }

        $topUsers = User::withCount(['activityLogs' => function($q) {
            $q->whereMonth('created_at', now()->month);
        }])->orderBy('activity_logs_count', 'desc')->take(10)->get();

        $topServices = Service::withCount('offers')->orderBy('offers_count', 'desc')->take(10)->get();

        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyStats[$date->format('Y-m')] = [
                'users' => User::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count(),
                'services' => Service::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count(),
                'requests' => \App\Models\ServiceRequest::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count(),
                'posts' => Post::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count(),
                'revenue' => \App\Models\Offer::where('status', 'accepted')->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->sum('price'),
            ];
        }

        return view('admin.reports-page', compact('topUsers', 'topServices', 'monthlyStats', 'months'));
    }

    public function backupPage()
    {
        $backups = [];
        $files = glob(public_path('backups/*.sql'));
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => round(filesize($file) / 1024, 2),
                'date' => date('Y/m/d H:i', filemtime($file))
            ];
        }
        return view('admin.backup', compact('backups'));
    }

    public function downloadBackup()
    {
        if (!file_exists(public_path('backups'))) {
            mkdir(public_path('backups'), 0755, true);
        }

        $filename = 'backup_' . date('Y_m_d_His') . '.sql';
        $path = public_path('backups/' . $filename);

        $command = "mysqldump --user=" . env('DB_USERNAME') . " --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . " > " . escapeshellarg($path);
        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($path)) {
            ActivityLogController::log(Auth::guard('admin')->id(), 'backup', 'إنشاء نسخة احتياطية');
            return response()->download($path)->deleteFileAfterSend(false);
        }

        return back()->with('error', 'فشل إنشاء النسخة الاحتياطية');
    }

    public function restoreBackup(Request $request)
    {
        $request->validate(['backup_file' => 'required|file|mimes:sql']);

        $file = $request->file('backup_file');
        $path = $file->getPathname();

        $command = "mysql --user=" . env('DB_USERNAME') . " --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . " < " . escapeshellarg($path);
        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            ActivityLogController::log(Auth::guard('admin')->id(), 'restore', 'استعادة نسخة احتياطية');
            return back()->with('success', 'تم استعادة النسخة الاحتياطية بنجاح!');
        }

        return back()->with('error', 'فشل استعادة النسخة الاحتياطية');
    }

    public function adminNotifications()
    {
        $adminTypes = ['admin', 'report', 'booking_new', 'offer_new', 'post_approved', 'work_delivered', 'rating_new'];
        
        $notifications = \App\Models\Notification::where('user_id', Auth::guard('admin')->id())
            ->whereIn('type', $adminTypes)
            ->latest()
            ->take(20)
            ->get();
        
        $unreadCount = \App\Models\Notification::where('user_id', Auth::guard('admin')->id())
            ->whereIn('type', $adminTypes)
            ->where('is_read', 0)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread' => $unreadCount
        ]);
    }

    public function markNotifRead($id)
    {
        \App\Models\Notification::where('id', $id)->update(['is_read' => 1]);
        return response()->json(['success' => true]);
    }

    public function exportReports()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=reports_' . date('Y_m_d') . '.csv',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, ['التقرير الشهري']);
            fputcsv($file, ['الشهر', 'مستخدمين جدد', 'خدمات', 'طلبات', 'منشورات', 'الإيرادات']);
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                fputcsv($file, [
                    $date->format('Y-m'),
                    User::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count(),
                    Service::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count(),
                    \App\Models\ServiceRequest::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count(),
                    Post::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count(),
                    \App\Models\Offer::where('status', 'accepted')->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->sum('price'),
                ]);
            }
            fputcsv($file, ['']);
            fputcsv($file, ['أكثر المستخدمين نشاطاً']);
            fputcsv($file, ['المستخدم', 'عدد النشاطات']);
            $topUsers = User::withCount(['activityLogs'])->orderBy('activity_logs_count', 'desc')->take(10)->get();
            foreach ($topUsers as $user) {
                fputcsv($file, [$user->name, $user->activity_logs_count]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function sendResetLink(Request $request)
{
    $request->validate(['email' => 'required|email']);
    
    $user = User::where('email', $request->email)->where('role', 'admin')->first();
    
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'البريد غير موجود أو ليس لديه صلاحية أدمن.']);
    }
    
    $token = bin2hex(random_bytes(32));
    
    \DB::table('password_resets')->insert([
        'email' => $user->email,
        'token' => $token,
        'created_at' => now()
    ]);
    
    $resetUrl = route('admin.password.reset', $token);
    
    ActivityLogController::log($user->id, 'password_reset', 'طلب استعادة كلمة المرور');
    
    return response()->json([
        'success' => true,
        'message' => 'تم إرسال رابط الاستعادة. (للتجربة: ' . $resetUrl . ')' 
    ]);
}

public function showResetForm($token)
{
    $reset = \DB::table('password_resets')->where('token', $token)->first();
    if (!$reset) return 'الرابط غير صالح أو منتهي.';
    return view('admin.reset-password', ['token' => $token, 'email' => $reset->email]);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);
    
    $reset = \DB::table('password_resets')
        ->where('token', $request->token)
        ->where('email', $request->email)
        ->first();
    
    if (!$reset) return back()->withErrors(['email' => 'الرابط غير صالح.']);
    
    User::where('email', $request->email)->update([
        'password' => \Hash::make($request->password)
    ]);
    
    \DB::table('password_resets')->where('email', $request->email)->delete();
    
    ActivityLogController::log(User::where('email', $request->email)->first()->id, 'password_reset', 'تم تغيير كلمة المرور');
    
    return redirect()->route('admin.login')->with('success', 'تم تغيير كلمة المرور. سجل دخول الآن.');
}

    public function approvePostByGet($id)
    {
        $post = Post::findOrFail($id);
        $post->update(['is_approved' => 1]);
        \App\Models\Notification::create([
            'user_id' => $post->user_id,
            'sender_id' => Auth::guard('admin')->id(),
            'type' => 'post_approved',
            'message' => '✅ تم قبول منشورك',
            'link' => route('post.show', $post->id)
        ]);
        ActivityLogController::log(Auth::guard('admin')->id(), 'approve_post', 'الموافقة على منشور #' . $post->id);
        return redirect()->route('admin.posts')->with('success', 'تم قبول المنشور وإشعار المستخدم');
    }

    public function ads()
    {
        $ads = Story::where('is_ad', 1)->with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.ads', compact('ads'));
    }

    public function approveAd($id)
    {
        $ad = Story::findOrFail($id);
        $ad->update(['status' => 'approved']);
        ActivityLogController::log(Auth::guard('admin')->id(), 'approve_ad', 'الموافقة على إعلان');
        return back()->with('success', 'تم قبول الإعلان');
    }

    public function rejectAd($id)
    {
        $ad = Story::findOrFail($id);
        $ad->update(['status' => 'rejected']);
        ActivityLogController::log(Auth::guard('admin')->id(), 'reject_ad', 'رفض إعلان');
        return back()->with('success', 'تم رفض الإعلان');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->where('role', 'admin')->first();
    
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'البريد غير موجود أو ليس أدمن']);
        }
    
        $code = rand(100000, 999999);
        
        \DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $code, 'created_at' => now()]
        );
    
        return response()->json(['success' => true, 'code' => $code, 'message' => 'تم إرسال الكود']);
    }
    
    public function verifyCode(Request $request)
    {
        $reset = \DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();
    
        if (!$reset) {
            return response()->json(['success' => false, 'message' => 'الكود غير صحيح']);
        }
    
        return response()->json(['success' => true]);
    }
    
    public function saveNewPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);
    
        User::where('email', $request->email)->update([
            'password' => \Hash::make($request->password)
        ]);
    
        \DB::table('password_resets')->where('email', $request->email)->delete();
    
        return response()->json(['success' => true]);
    }

    public function revenue()
{
    $packageQuery = \App\Models\UserPackage::where('price_paid', '>', 0);
    
    $todayPackages = (clone $packageQuery)->whereDate('created_at', today())->sum('price_paid');
    $weekPackages = (clone $packageQuery)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('price_paid');
    $monthPackages = (clone $packageQuery)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('price_paid');
    $yearPackages = (clone $packageQuery)->whereYear('created_at', now()->year)->sum('price_paid');
    $totalPackages = (clone $packageQuery)->sum('price_paid');

    $todayAds = \App\Models\Story::where('is_ad', 1)->where('status', 'approved')->whereDate('created_at', today())->sum('ad_price');
    $weekAds = \App\Models\Story::where('is_ad', 1)->where('status', 'approved')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('ad_price');
    $monthAds = \App\Models\Story::where('is_ad', 1)->where('status', 'approved')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('ad_price');
    $yearAds = \App\Models\Story::where('is_ad', 1)->where('status', 'approved')->whereYear('created_at', now()->year)->sum('ad_price');
    $totalAds = \App\Models\Story::where('is_ad', 1)->where('status', 'approved')->sum('ad_price');

    $todayServices = \App\Models\Offer::where('status', 'accepted')->whereDate('created_at', today())->sum('price');
    $weekServices = \App\Models\Offer::where('status', 'accepted')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('price');
    $monthServices = \App\Models\Offer::where('status', 'accepted')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('price');
    $yearServices = \App\Models\Offer::where('status', 'accepted')->whereYear('created_at', now()->year)->sum('price');
    $totalServices = \App\Models\Offer::where('status', 'accepted')->sum('price');

    $todayRevenue = $todayPackages + $todayAds + $todayServices;
    $weekRevenue = $weekPackages + $weekAds + $weekServices;
    $monthRevenue = $monthPackages + $monthAds + $monthServices;
    $yearRevenue = $yearPackages + $yearAds + $yearServices;
    $totalRevenue = $totalPackages + $totalAds + $totalServices;

    return view('admin.revenue', compact(
        'todayRevenue', 'weekRevenue', 'monthRevenue', 'yearRevenue', 'totalRevenue',
        'todayPackages', 'todayAds', 'todayServices',
        'weekPackages', 'weekAds', 'weekServices',
        'monthPackages', 'monthAds', 'monthServices',
        'yearPackages', 'yearAds', 'yearServices',
        'totalPackages', 'totalAds', 'totalServices'
    ));
}

}