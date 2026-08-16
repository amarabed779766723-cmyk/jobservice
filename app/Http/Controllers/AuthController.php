<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\EmailVerification;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin()
    {
        $bannedUser = null;
        $bannedMessage = null;
        
        if (session('banned_email')) {
            $bannedUser = User::where('email', session('banned_email'))->first();
            $bannedMessage = session('banned_message');
        }
        
        $suspendedEmail = session('suspended_email');
        $suspendedMessage = session('suspended_message');
        
        return view('auth.login', compact('bannedUser', 'bannedMessage', 'suspendedEmail', 'suspendedMessage'));
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        if ($user && ($user->status ?? 'active') === 'banned') {
            return back()
                ->with('banned_email', $user->email)
                ->with('banned_message', 'تم حظر هذا الحساب من قبل إدارة المنصة.')
                ->withInput();
        }
        
        if ($user && $user->is_active == 0) {
            return back()
                ->with('suspended_email', $user->email)
                ->with('suspended_message', '⚠️ حسابك موقّف مؤقتاً. يمكنك تفعيله من خلال تسجيل الدخول.')
                ->withInput();
        }
        
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials, $request->remember)) {
            session(['user_type' => 'platform']);  // ✅ جلسة المنصة
            ActivityLogController::log(Auth::id(), 'login', 'تسجيل دخول');
            return redirect('/home');
        }
        
        if ($user && is_null($user->email_verified_at)) {
            return back()->withErrors([
                'email' => 'يرجى تفعيل حسابك عبر البريد الإلكتروني. <a href="' . route('verify.resend.form') . '?email=' . $user->email . '">إعادة إرسال رابط التحقق</a>'
            ])->withInput();
        }
        
        return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])->withInput();
    }
    
    public function showRegister()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\p{Arabic}\p{Latin}\s]+$/u'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'regex:/^\+967\d{9}$/', 'unique:users'],
            'birthdate' => ['required', 'date', 'before_or_equal:' . date('Y-m-d', strtotime('-16 years'))],
            'password' => ['required', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/'],
            'user_type' => ['required', 'in:client,provider'],
        ], [
            'name.regex' => 'الاسم يجب أن يحتوي على حروف فقط.',
            'phone.regex' => 'رقم الجوال يجب أن يبدأ بـ +967 ويتكون من 9 أرقام بعده.',
            'birthdate.before_or_equal' => 'يجب أن يكون عمرك 16 سنة فأكثر.',
            'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير وحرف صغير ورقم (8 خانات على الأقل).',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'birthdate' => $request->birthdate,
            'user_type' => $request->user_type,
        ]);
        
        // إعطاء الباقة المجانية تلقائياً
        $freePackage = \App\Models\Package::where('price', 0)->first();
        if ($freePackage) {
            \App\Models\UserPackage::create([
                'user_id' => $user->id,
                'package_id' => $freePackage->id,
                'start_date' => now(),
                'end_date' => now()->addDays($freePackage->duration_days),
                'status' => 'active'
            ]);
        }
        
        // توليد كود عشوائي
        $code = rand(100000, 999999);
        
        // تخزين الكود
        EmailVerification::create([
            'email' => $request->email,
            'code' => $code,
            'created_at' => now(),
        ]);
        
        // ✅ إرسال الكود عبر البريد الإلكتروني
        Mail::to($request->email)->send(new VerificationCodeMail($code));
        
        // تخزين في الجلسة
        session([
            'verification_email' => $request->email,
            'verification_code' => $code,
        ]);
        
        return redirect()->route('verify.code.form')->with('success', '✅ تم إرسال كود التفعيل إلى بريدك الإلكتروني.');
    }
    
    public function logout()
    {
        if (Auth::check()) {
            ActivityLogController::log(Auth::id(), 'logout', 'تسجيل خروج');
        }
        
        Auth::logout();
        return redirect('/login');
    }

    public function activatePackage(Request $request)
    {
        $package = \App\Models\Package::findOrFail($request->package_id);
        
        // تعطيل الباقات القديمة
        \App\Models\UserPackage::where('user_id', Auth::id())->update(['status' => 'expired']);
        
        // ✅ إنشاء اشتراك جديد مع التاريخ الكامل
        \App\Models\UserPackage::create([
            'user_id' => Auth::id(),
            'package_id' => $package->id,
            'start_date' => now(),
            'end_date' => now()->addDays($package->duration_days),
            'status' => 'active',
            'price_paid' => $package->price,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        ActivityLogController::log(Auth::id(), 'activate_package', 'تفعيل باقة: ' . $package->name . ' - السعر: ' . $package->price);
        
        return back()->with('success', '✅ تم ترقية باقتك إلى ' . $package->name . ' بنجاح!');
    }
}