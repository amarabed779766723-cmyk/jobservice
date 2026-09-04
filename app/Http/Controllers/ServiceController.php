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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        // ✅ رفع الصورة
        $imageName = 'service-default.png';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'service_' . time() . '_' . auth()->id() . '.' . $image->extension();
            $image->move(public_path('uploads/services'), $imageName);
        }
        
        Service::create([
            'provider_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'image' => $imageName,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'city' => $request->city,
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
    
    public function nearby(Request $request)
    {
        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->radius ?? 50;
        
        if (!$lat || !$lng) {
            return response()->json([
                'success' => false,
                'message' => 'الموقع غير متوفر'
            ]);
        }
        
        $services = Service::nearby($lat, $lng, $radius);
        
        return response()->json([
            'success' => true,
            'data' => $services,
            'count' => $services->count()
        ]);
    }
}