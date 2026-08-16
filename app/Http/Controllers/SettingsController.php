<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function privacy(Request $request)
    {
        $user = Auth::user();
        $settings = json_decode(DB::table('users')->where('id', $user->id)->value('settings') ?? '{}', true);
        $settings['is_private'] = $request->has('is_private');
        DB::table('users')->where('id', $user->id)->update(['settings' => json_encode($settings)]);
        return back()->with('success', 'تم تحديث الخصوصية');
    }

    public function chatLock(Request $request)
    {
        $user = Auth::user();
        $settings = json_decode(DB::table('users')->where('id', $user->id)->value('settings') ?? '{}', true);
        if ($request->chat_lock_code) {
            $settings['chat_lock_code'] = $request->chat_lock_code;
        } else {
            unset($settings['chat_lock_code']);
        }
        DB::table('users')->where('id', $user->id)->update(['settings' => json_encode($settings)]);
        return back()->with('success', 'تم تحديث قفل الدردشة');
    }

    public function chatWallpaper(Request $request)
    {
        $user = Auth::user();
        $settings = json_decode(DB::table('users')->where('id', $user->id)->value('settings') ?? '{}', true);
        if ($request->hasFile('chat_wallpaper')) {
            $file = $request->file('chat_wallpaper');
            $filename = 'wallpaper_' . $user->id . '_' . time() . '.' . $file->extension();
            $file->move(public_path('uploads/chat_wallpapers'), $filename);
            $settings['chat_wallpaper'] = $filename;
        }
        DB::table('users')->where('id', $user->id)->update(['settings' => json_encode($settings)]);
        return back()->with('success', 'تم تغيير خلفية الدردشة');
    }
}