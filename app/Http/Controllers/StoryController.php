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
        $request->validate([
            'image' => 'required|file|max:51200',
            'package_id' => 'required|exists:ad_packages,id'
        ], [
            'package_id.required' => 'يرجى اختيار باقة للإعلان.',
        ]);

        $package = \App\Models\AdPackage::find($request->package_id);
        if (!$package) return back()->with('error', 'الباقة غير موجودة.');

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
            'ad_price' => $package->price,  // ✅ إضافة السعر
        ]);

        // ✅ إنشاء معاملة مالية pending
        \App\Models\Transaction::create([
            'user_id' => Auth::id(),
            'package_id' => null,
            'amount' => $package->price,
            'type' => 'ad',
            'status' => 'pending',
            'sender_name' => Auth::user()->name,
            'sender_phone' => Auth::user()->phone,
        ]);

        // ✅ إشعار للأدمن بالدفع المعلق
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'sender_id' => Auth::id(),
                'type' => 'payment_pending',
                'message' => '📢 إعلان جديد من ' . Auth::user()->name . ' - ' . $package->name . ' - ' . $package->price . ' ر.ي',
                'link' => route('admin.transactions'),
            ]);
        }

        ActivityLogController::log(Auth::id(), 'create_ad', 'رفع إعلان جديد - ' . $package->name . ' - ' . $package->price . ' ر.ي');
        ActivityLogController::notifyAdmin(Auth::user()->name . ' رفع إعلان جديد - ' . $package->name . ' - ' . $package->price . ' ر.ي - انتظار تأكيد الدفع', route('admin.transactions'));
    
        return redirect()->route('stories.index')->with('success', '✅ تم رفع الإعلان! سيتم نشره بعد تأكيد الدفع.');
    }

    public function getUserStories($userId)
    {
        $stories = Story::where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'user' => $stories->first() ? [
                'id' => $stories->first()->user->id ?? null,
                'name' => $stories->first()->user->name ?? 'مستخدم',
                'avatar' => $stories->first()->user->avatar ?? 'default-avatar.png',
            ] : null,
            'stories' => $stories->map(function($story) {
                return [
                    'id' => $story->id,
                    'image' => asset('uploads/stories/' . $story->image),
                    'caption' => $story->caption,
                    'is_video' => $this->isVideo($story->image),
                    'created_at' => Carbon::parse($story->created_at)->diffForHumans(),
                ];
            })
        ]);
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