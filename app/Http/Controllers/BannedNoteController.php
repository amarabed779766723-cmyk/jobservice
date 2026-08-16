<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BannedNote;

class BannedNoteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email', 'message' => 'required|string|max:500']);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            BannedNote::create(['user_id' => $user->id, 'message' => $request->message]);
        }
        return back()->with('note_sent', true);
    }
}