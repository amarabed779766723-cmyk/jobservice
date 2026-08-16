<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use Carbon\Carbon;

class VerifyCodeController extends Controller
{
    public function showForm()
    {
        $email = session('verification_email');
        
        if (!$email) {
            return redirect('/login')->with('error', 'لم يتم العثور على بريد إلكتروني للتحقق.');
        }
        
        return view('auth.verify-code', compact('email'));
    }
    
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
        ]);
        
        $verification = EmailVerification::where('email', $request->email)
            ->where('code', $request->code)
            ->first();
        
        if (!$verification) {
            return back()->withErrors(['code' => 'الكود غير صحيح. حاول مرة أخرى.']);
        }
        
        $createdAt = Carbon::parse($verification->created_at);
        if ($createdAt->diffInMinutes(now()) > 10) {
            $verification->delete();
            return back()->withErrors(['code' => 'انتهت صلاحية الكود. يرجى طلب كود جديد.']);
        }
        
        $user = User::where('email', $request->email)->first();
        $user->email_verified_at = now();
        $user->save();
        
        $verification->delete();
        
        Auth::login($user);
        
        return redirect('/home')->with('success', '✅ تم تفعيل حسابك بنجاح!');
    }
    
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        
        EmailVerification::where('email', $request->email)->delete();
        
        $code = rand(100000, 999999);
        
        EmailVerification::create([
            'email' => $request->email,
            'code' => $code,
            'created_at' => now(),
        ]);
        
        Mail::to($request->email)->send(new VerificationCodeMail($code));
        
        session(['verification_code' => $code]);
        
        return back()->with('success', '✅ تم إرسال كود جديد إلى بريدك الإلكتروني.');
    }
}