<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordResetCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetCodeMail;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;

        PasswordResetCode::where('email', $email)->delete();

        $code = rand(100000, 999999);

        PasswordResetCode::create([
            'email' => $email,
            'code' => $code,
            'created_at' => now(),
        ]);

        Mail::to($email)->send(new PasswordResetCodeMail($code));

        session([
            'reset_email' => $email,
            'reset_code' => $code,
        ]);

        return redirect()->route('password.reset.code.form')->with('success', '✅ تم إرسال كود إعادة التعيين إلى بريدك الإلكتروني.');
    }

    public function showCodeForm()
    {
        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('password.request')->with('error', 'لم يتم العثور على بريد إلكتروني.');
        }

        return view('auth.reset-code', compact('email'));
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
        ]);

        $reset = PasswordResetCode::where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if (!$reset) {
            return back()->withErrors(['code' => 'الكود غير صحيح. حاول مرة أخرى.']);
        }

        $createdAt = Carbon::parse($reset->created_at);
        if ($createdAt->diffInMinutes(now()) > 10) {
            $reset->delete();
            return back()->withErrors(['code' => 'انتهت صلاحية الكود. يرجى طلب كود جديد.']);
        }

        $reset->delete();

        session(['reset_verified_email' => $request->email]);

        return redirect()->route('password.reset.form')->with('success', '✅ تم التحقق من الكود. يمكنك الآن إعادة تعيين كلمة المرور.');
    }

    public function showResetForm()
    {
        $email = session('reset_verified_email');

        if (!$email) {
            return redirect()->route('password.request')->with('error', 'لم يتم التحقق من هويتك. يرجى المحاولة مرة أخرى.');
        }

        return view('auth.reset-password', compact('email'));
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        session()->forget(['reset_email', 'reset_verified_email', 'reset_code']);

        return redirect()->route('login')->with('status', '✅ تم إعادة تعيين كلمة المرور بنجاح! يمكنك تسجيل الدخول الآن.');
    }

    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->email;

        PasswordResetCode::where('email', $email)->delete();

        $code = rand(100000, 999999);

        PasswordResetCode::create([
            'email' => $email,
            'code' => $code,
            'created_at' => now(),
        ]);

        Mail::to($email)->send(new PasswordResetCodeMail($code));

        session([
            'reset_email' => $email,
            'reset_code' => $code,
        ]);
        
        return redirect()->route('password.reset.code.form')->with('success', '✅ كود إعادة التعيين: ' . $code);
        
    }
}