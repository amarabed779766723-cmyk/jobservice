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
            ->with('suspended_message', '⚠️ حسابك موقّف مؤقتاً.')
            ->withInput();
    }
    
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials, $request->remember)) {
        
        // ✅ التحقق من التوثيق - فقط للجدد اللي رفعوا وثيقة
        if (Auth::user()->user_type === 'provider') {
            $verification = \App\Models\UserVerification::where('user_id', Auth::id())
                ->latest()
                ->first();
            
            // ✅ ما رفع وثيقة = حساب قديم = يدخل عادي
            if ($verification && $verification->status === 'pending') {
                Auth::logout();
                return back()->withErrors(['email' => '⏳ وثيقتك قيد المراجعة - انتظر موافقة الإدارة'])->withInput();
            }
            
            if ($verification && $verification->status === 'rejected') {
                Auth::logout();
                return back()->withErrors(['email' => '❌ تم رفض وثيقتك. السبب: ' . ($verification->admin_note ?? 'غير محدد')])->withInput();
            }
        }
        
        session(['user_type' => 'platform']);
        ActivityLogController::log(Auth::id(), 'login', 'تسجيل دخول');
        return redirect('/home');
    }
    
    if ($user && is_null($user->email_verified_at)) {
        return back()->withErrors([
            'email' => 'يرجى تفعيل حسابك عبر البريد الإلكتروني.'
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
        
        // ✅ التحقق من التوثيق لمقدمي الخدمة
        if ($request->user_type === 'provider') {
            $request->validate([
                'verification_type' => 'required|in:national_id,university_certificate,professional_certificate',
                'document_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            ], [
                'verification_type.required' => 'يجب اختيار نوع التوثيق',
                'document_image.required' => 'يجب رفع صورة المستند',
            ]);
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'birthdate' => $request->birthdate,
            'user_type' => $request->user_type,
        ]);
        
        // ✅ حفظ وثيقة التوثيق لمقدمي الخدمة
        if ($request->user_type === 'provider' && $request->hasFile('document_image')) {
            $image = $request->file('document_image');
            $imageName = 'verify_' . time() . '_' . rand(1000, 9999) . '.' . $image->extension();
            $image->move(public_path('uploads/verifications'), $imageName);
            
            \App\Models\UserVerification::create([
                'user_id' => $user->id,
                'verification_type' => $request->verification_type,
                'document_image' => $imageName,
                'status' => 'pending',
                'created_at' => now(),
            ]);
            
            // إشعار للأدمن
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'sender_id' => $user->id,
                    'type' => 'verification_pending',
                    'message' => '🪪 توثيق جديد من ' . $user->name . ' (مقدم خدمة)',
                    'link' => route('admin.verifications'),
                ]);
            }
        }
        
        // إعطاء الباقة المجانية تلقائياً
        $freePackage = \App\Models\Package::where('price', 0)->first();
        if ($freePackage) {
            \App\Models\UserPackage::create([
                'user_id' => $user->id,
                'package_id' => $freePackage->id,
                'start_date' => now(),
                'end_date' => now()->addDays($freePackage->duration_days),
                'status' => 'active',
                'created_at' => now(),
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
        
        // إرسال الكود - مع حماية من الخطأ
        try {
            Mail::to($request->email)->send(new VerificationCodeMail($code));
        } catch (\Exception $e) {
            // تجاهل خطأ البريد
        }
        
        // تخزين في الجلسة
        session([
            'verification_email' => $request->email,
            'verification_code' => $code,
        ]);
        
        return redirect()->route('verify.code.form')->with('success', '✅ كود التفعيل: ' . $code);
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
        
        if ($package->price == 0) {
            \App\Models\UserPackage::where('user_id', Auth::id())->update(['status' => 'expired']);
            
            \App\Models\UserPackage::create([
                'user_id' => Auth::id(),
                'package_id' => $package->id,
                'start_date' => now(),
                'end_date' => now()->addDays($package->duration_days),
                'status' => 'active',
                'created_at' => now(),
            ]);
            
            return back()->with('success', '✅ تم تفعيل الباقة المجانية!');
        }
        
        $request->validate([
            'sender_name' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:20',
            'receipt_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        $image = $request->file('receipt_image');
        $imageName = time() . '_' . Auth::id() . '.' . $image->extension();
        $image->move(public_path('uploads/receipts'), $imageName);
        
        \App\Models\Transaction::create([
            'user_id' => Auth::id(),
            'package_id' => $package->id,
            'amount' => $package->price,
            'type' => 'package',
            'status' => 'pending',
            'sender_name' => $request->sender_name,
            'sender_phone' => $request->sender_phone,
            'receipt_image' => $imageName,
        ]);
        
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'sender_id' => Auth::id(),
                'type' => 'payment_pending',
                'message' => '💳 دفعة جديدة من ' . Auth::user()->name . ' - ' . $package->name . ' - ' . $package->price . ' ر.ي',
                'link' => route('admin.transactions'),
            ]);
        }
        
        ActivityLogController::log(Auth::id(), 'payment_pending', 'طلب شراء باقة: ' . $package->name . ' - ' . $package->price . ' ر.ي');
        
        return back()->with('success', '✅ تم استلام طلبك! سيتم تفعيل الباقة بعد تأكيد الدفع.');
    }
}