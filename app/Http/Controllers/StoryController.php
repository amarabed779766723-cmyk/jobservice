<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Story;
use App\Models\StoryView;
use App\Models\Follow;
use Carbon\Carbon;

class StoryController extends Controller
{
    public function index()
    {
        Story::where('expires_at', '<', Carbon::now())->delete();
    
        $myStories = Story::where('user_id', Auth::id())
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    
        return view('stories.index', compact('myStories'));
    }

    public function store(Request $request)
{
    // ✅ التحقق من الباقة الأساسية
    $activePackage = \App\Models\UserPackage::where('user_id', Auth::id())
        ->where('status', 'active')
        ->first();

    $userPackage = null;
    if ($activePackage && !\Carbon\Carbon::parse($activePackage->end_date)->isPast()) {
        $userPackage = \App\Models\Package::find($activePackage->package_id);
    }

    // ✅ حساب الإعلانات المستهلكة من الباقة الحالية
    $currentAds = Story::where('user_id', Auth::id())
        ->where('is_ad', 1)
        ->where('created_at', '>=', $activePackage ? $activePackage->start_date : now()->subYears(10))
        ->count();

    // ✅ إذا عنده إعلانات متبقية في الباقة → بدون دفع
    if ($userPackage && $userPackage->max_ads !== null && $currentAds < $userPackage->max_ads) {
        
        // ✅ بدون دفع - فقط رفع الإعلان
        $request->validate([
            'image' => 'required|file|max:51200',
        ], [
            'image.required' => 'يرجى رفع صورة الإعلان.',
        ]);

        $file = $request->file('image');
        $filename = 'ad_' . time() . '.' . $file->extension();
        $destinationPath = public_path('uploads/stories');
        
        if (str_starts_with($file->getMimeType(), 'video/') && $request->has('trim_start')) {
            $trimStart = (float) $request->trim_start;
            $trimEnd = (float) $request->trim_end;
            
            $tempPath = $file->getPathname();
            $outputFilename = 'ad_' . time() . '.mp4';
            $outputPath = $destinationPath . '/' . $outputFilename;
            
            $cmd = "ffmpeg -i " . escapeshellarg($tempPath) . " -ss {$trimStart} -to {$trimEnd} -c:v libx264 -c:a aac -preset fast " . escapeshellarg($outputPath) . " 2>&1";
            exec($cmd, $output, $returnCode);
            
            if ($returnCode !== 0) {
                $file->move($destinationPath, $filename);
            } else {
                $filename = $outputFilename;
            }
        } else {
            $file->move($destinationPath, $filename);
        }
        
        $story = Story::create([
            'user_id' => Auth::id(),
            'image' => $filename,
            'caption' => $request->caption,
            'expires_at' => Carbon::now()->addDays(30), // ✅ 30 يوم افتراضي
            'is_ad' => 1,
            'package_id' => $activePackage->package_id, // ✅ باقة المستخدم
            'status' => 'pending',
            'link' => $request->link,
            'ad_price' => 0, // ✅ بدون سعر
        ]);

        // ✅ إشعار للأدمن
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'sender_id' => Auth::id(),
                'type' => 'ad_pending',
                'message' => '📢 إعلان جديد من ' . Auth::user()->name . ' (من الباقة) - بانتظار الموافقة',
                'link' => route('admin.ads'),
            ]);
        }

        ActivityLogController::log(Auth::id(), 'create_ad', 'رفع إعلان من الباقة');
        ActivityLogController::notifyAdmin(Auth::user()->name . ' رفع إعلان من الباقة - بانتظار الموافقة', route('admin.ads'));
        
        return redirect()->route('stories.index')->with('success', '✅ تم رفع الإعلان! سيتم نشره بعد موافقة الأدمن.');
    }

    // ✅ إذا استهلك الإعلانات أو ما عنده باقة → لازم يدفع
    if ($userPackage && $userPackage->max_ads !== null && $currentAds >= $userPackage->max_ads) {
        return back()->with('error', '⚠️ لقد استهلكت كل الإعلانات المتاحة في باقتك (' . $userPackage->max_ads . ' إعلانات). للاستمرار، اشتر باقة إعلانات جديدة.');
    }

    // ✅ إذا ما عنده باقة → لازم يدفع
    $request->validate([
        'image' => 'required|file|max:51200',
        'package_id' => 'required|exists:ad_packages,id',
        'wallet_name' => 'required|string',
        'receipt_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ], [
        'package_id.required' => 'يرجى اختيار باقة للإعلان.',
        'wallet_name.required' => 'يرجى اختيار المحفظة.',
        'receipt_image.required' => 'يرجى رفع صورة إثبات التحويل.',
    ]);

    $package = \App\Models\AdPackage::find($request->package_id);
    if (!$package) return back()->with('error', 'الباقة غير موجودة.');

    // ✅ رفع صورة الإثبات
    if ($request->hasFile('receipt_image')) {
        $receiptImage = $request->file('receipt_image');
        $receiptName = 'ad_receipt_' . time() . '_' . Auth::id() . '.' . $receiptImage->extension();
        $receiptImage->move(public_path('uploads/receipts'), $receiptName);
    } else {
        $receiptName = null;
    }

    $file = $request->file('image');
    $filename = 'ad_' . time() . '.' . $file->extension();
    $destinationPath = public_path('uploads/stories');
    
    if (str_starts_with($file->getMimeType(), 'video/') && $request->has('trim_start')) {
        $trimStart = (float) $request->trim_start;
        $trimEnd = (float) $request->trim_end;
        
        $tempPath = $file->getPathname();
        $outputFilename = 'ad_' . time() . '.mp4';
        $outputPath = $destinationPath . '/' . $outputFilename;
        
        $cmd = "ffmpeg -i " . escapeshellarg($tempPath) . " -ss {$trimStart} -to {$trimEnd} -c:v libx264 -c:a aac -preset fast " . escapeshellarg($outputPath) . " 2>&1";
        exec($cmd, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $file->move($destinationPath, $filename);
        } else {
            $filename = $outputFilename;
        }
    } else {
        $file->move($destinationPath, $filename);
    }

    $story = Story::create([
        'user_id' => Auth::id(),
        'image' => $filename,
        'caption' => $request->caption,
        'expires_at' => Carbon::now()->addDays($package->duration_days),
        'is_ad' => 1,
        'package_id' => $package->id,
        'status' => 'pending',
        'link' => $request->link,
        'ad_price' => $package->price,
    ]);

    \App\Models\Transaction::create([
        'user_id' => Auth::id(),
        'package_id' => null,
        'amount' => $package->price,
        'type' => 'ad',
        'status' => 'pending',
        'sender_name' => $request->sender_name,
        'sender_phone' => $request->sender_phone,
        'wallet_name' => $request->wallet_name,
        'receipt_image' => $receiptName,
    ]);

    $admins = \App\Models\User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        \App\Models\Notification::create([
            'user_id' => $admin->id,
            'sender_id' => Auth::id(),
            'type' => 'payment_pending',
            'message' => '📢 إعلان مدفوع من ' . $request->sender_name . ' - ' . $package->name . ' - ' . $package->price . ' ر.ي',
            'link' => route('admin.transactions'),
        ]);
    }

    ActivityLogController::log(Auth::id(), 'create_ad', 'رفع إعلان مدفوع - ' . $package->name . ' - ' . $package->price . ' ر.ي');
    ActivityLogController::notifyAdmin($request->sender_name . ' رفع إعلان مدفوع - ' . $package->name . ' - ' . $package->price . ' ر.ي - انتظار تأكيد الدفع', route('admin.transactions'));
    
    return redirect()->route('stories.index')->with('success', '✅ تم رفع الإعلان! سيتم نشره بعد تأكيد الدفع.');
}
    public function markViewed($storyId)
    {
        StoryView::firstOrCreate([
            'story_id' => $storyId,
            'user_id' => Auth::id()
        ]);

        return response()->json(['success' => true]);
    }

    public function getViews($storyId)
    {
        $story = Story::findOrFail($storyId);
        $views = StoryView::where('story_id', $storyId)->with('user')->get();
        $isOwner = ($story->user_id == Auth::id());

        return response()->json([
            'count' => $views->count(),
            'is_owner' => $isOwner,
            'viewers' => $isOwner ? $views->map(function($view) {
                return [
                    'id' => $view->user->id,
                    'name' => $view->user->name,
                    'avatar' => $view->user->avatar ?? 'default-avatar.png',
                ];
            }) : []
        ]);
    }

    private function isVideo($filename)
    {
        $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, $videoExtensions);
    }

    public function delete($storyId)
    {
        $story = Story::where('id', $storyId)->where('user_id', Auth::id())->firstOrFail();
        
        $path = public_path('uploads/stories/' . $story->image);
        if (file_exists($path)) {
            unlink($path);
        }
        
        $story->delete();

        ActivityLogController::log(Auth::id(), 'delete_story', 'حذف قصة');
        
        return redirect()->route('stories.index')->with('success', 'تم حذف القصة');
    }
}