<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;

class ActivityLogController extends Controller
{
    public static function log($userId, $action, $description = null)
    {
        ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function notifyAdmin($message, $link = null)
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'sender_id' => null,
                'type' => 'admin',
                'message' => $message,
                'link' => $link
            ]);
        }
    }
    
    public function index()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(50);
        return view('admin.activity-logs', compact('logs'));
    }
    
    public function delete($id)
    {
        ActivityLog::findOrFail($id)->delete();
        return back()->with('success', 'تم حذف السجل');
    }
    
    public function clearAll()
    {
        ActivityLog::truncate();
        return back()->with('success', 'تم مسح جميع السجلات');
    }
}