<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;

class ChatController extends Controller
{
    public function index()
{
    $chats = Chat::whereHas('participants', function($q) {
        $q->where('user_id', Auth::id());
    })->with(['participants' => function($q) {
        $q->where('id', '!=', Auth::id());
    }, 'messages' => function($q) {
        $q->latest()->take(1);
    }])->get()->filter(function($chat) {
        return $chat->participants->count() > 0;
    })->sortByDesc(function($chat) {
        return $chat->messages->first()->created_at ?? $chat->created_at;
    });
    return view('chat.index', compact('chats'));
}

public function show($id)
{
    $chat = Chat::with(['participants', 'messages.sender', 'messages.replyTo'])->findOrFail($id);
    
    // ✅ تعليم الرسائل كمقروءة عند فتح المحادثة
    Message::where('chat_id', $id)
        ->where('sender_id', '!=', Auth::id())
        ->where('is_read', 0)
        ->update(['is_read' => 1]);
    
    if (Auth::user()->role === 'admin') {
        $otherUser = $chat->participants->first() ?? new User();
        $messages = $chat->messages()->with('sender', 'replyTo')->orderBy('created_at', 'asc')->get();
        return view('chat.show', compact('chat', 'messages', 'otherUser'));
    }
    if (!$chat->participants->contains(Auth::id())) abort(403);
    $otherUser = $chat->participants->where('id', '!=', Auth::id())->first();
    $messages = $chat->messages()->with('sender', 'replyTo')->orderBy('created_at', 'asc')->get();
    return view('chat.show', compact('chat', 'messages', 'otherUser'));
}

    public function start($userId)
    {
        if ($userId == Auth::id()) return redirect()->route('chats.index');
        $existing = Chat::whereHas('participants', function($q) use ($userId) {
            $q->where('user_id', Auth::id());
        })->whereHas('participants', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->first();
        if ($existing) return redirect()->route('chat.show', $existing->id);
        $chat = Chat::create();
        $chat->participants()->attach([Auth::id(), $userId]);
        return redirect()->route('chat.show', $chat->id);
    }

    public function sendMessage(Request $request, $id)
    {
        $message = new Message();
        $message->chat_id = $id;
        $message->sender_id = Auth::id();
        $message->message_text = $request->message_text;
        $message->reply_to = $request->reply_to;
        $message->created_at = now();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'chat_img_' . time() . '.' . $file->extension();
            $file->move(public_path('uploads/chats'), $filename);
            $message->image = $filename;
        }

        if ($request->hasFile('file_attachment')) {
            $file = $request->file('file_attachment');
            $filename = 'chat_file_' . time() . '.' . $file->extension();
            $file->move(public_path('uploads/chats'), $filename);
            $message->file_attachment = $filename;
            $message->file_name = $file->getClientOriginalName();
        }

        $message->save();

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'message_text' => $message->message_text,
                'image' => $message->image ? asset('uploads/chats/' . $message->image) : null,
                'file_attachment' => $message->file_attachment ? asset('uploads/chats/' . $message->file_attachment) : null,
                'file_name' => $message->file_name,
                'reply_to' => $message->reply_to,
                'sender' => ['name' => Auth::user()->name]
            ]
        ]);
    }

    public function deleteMessage($id)
    {
        $message = Message::where('id', $id)->where('sender_id', Auth::id())->firstOrFail();
        $message->delete();
        return response()->json(['success' => true]);
    }

    public function editMessage(Request $request, $id)
    {
        $message = Message::where('id', $id)->where('sender_id', Auth::id())->firstOrFail();
        $message->message_text = $request->message_text;
        $message->save();
        return response()->json(['success' => true]);
    }
    public function settings($id)
{
    $chat = Chat::findOrFail($id);
    return view('chat.settings', compact('chat'));
}

public function saveSettings(Request $request, $id)
{
    $chat = Chat::findOrFail($id);
    $user = Auth::user();
    $settings = json_decode($user->settings ?? '{}', true);

    if ($request->type == 'color') {
        $settings['chat_wallpaper'] = $request->value;
    } elseif ($request->type == 'image' && $request->hasFile('wallpaper_image')) {
        $file = $request->file('wallpaper_image');
        $filename = 'wallpaper_' . time() . '.' . $file->extension();
        $file->move(public_path('uploads/chat_wallpapers'), $filename);
        $settings['chat_wallpaper'] = $filename;
    } elseif ($request->type == 'font_size') {
        $settings['font_size'] = $request->value;
    } elseif ($request->type == 'delete_chat') {
        Message::where('chat_id', $id)->delete();
        return redirect()->route('chat.show', $id)->with('success', 'تم حذف جميع الرسائل');
    }

    $user->settings = json_encode($settings);
    $user->save();

    // إرجاع JSON للتطبيقات عبر AJAX
    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'تم حفظ الإعدادات',
            'wallpaper' => $settings['chat_wallpaper'] ?? null,
            'font_size' => $settings['font_size'] ?? 'medium'
        ]);
    }

    return redirect()->route('chat.show', $id)->with('success', 'تم حفظ الإعدادات');
}
}