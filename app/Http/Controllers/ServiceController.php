<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('provider')
            ->latest()
            ->paginate(12);
        
        return view('services.index', compact('services'));
    }
    
    public function create()
    {
        return view('service-create');
    }
    
    public function store(Request $request)
    {
        // تفتيش الباقة
        $activePackage = \App\Models\UserPackage::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (!$activePackage || \Carbon\Carbon::parse($activePackage->end_date)->isPast()) {
            if ($activePackage) $activePackage->update(['status' => 'expired']);
            return back()->with('error', '⚠️ باقتك منتهية. <a href="' . route('packages') . '" style="color:#2563eb; font-weight:700;">ترقية الباقة</a>');
        }

        $package = \App\Models\Package::find($activePackage->package_id);
        $currentServices = Service::where('provider_id', auth()->id())->count();

        if ($package->max_services !== null && $currentServices >= $package->max_services) {
            return back()->with('error', '⚠️ لقد وصلت للحد الأقصى (' . $package->max_services . ' خدمات) في باقتك "' . $package->name . '". <a href="' . route('packages') . '" style="color:#2563eb; font-weight:700;">ترقية إلى باقة أعلى</a>');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:1',
        ]);
        
        Service::create([
            'provider_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
        ]);

        ActivityLogController::log(auth()->id(), 'create_service', 'إنشاء خدمة جديدة');
        ActivityLogController::notifyAdmin(auth()->user()->name . ' أضاف خدمة جديدة: ' . $request->title, route('admin.services'));
        
        return redirect()->route('home')->with('success', '✅ تم إنشاء الخدمة بنجاح!');
    }
    
    public function show($id)
    {
        $service = Service::with('provider')->findOrFail($id);
        return view('service-show', compact('service'));
    }
}