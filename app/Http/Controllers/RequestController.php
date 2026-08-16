<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use App\Models\Offer;
use App\Models\Rating;
use App\Models\Notification;

class RequestController extends Controller
{
    public function create()
    {
        return view('request-create');
    }
    
    public function store(Request $request)
{
    $activePackage = \App\Models\UserPackage::where('user_id', auth()->id())
        ->where('status', 'active')
        ->first();

    if (!$activePackage || \Carbon\Carbon::parse($activePackage->end_date)->isPast()) {
        if ($activePackage) $activePackage->update(['status' => 'expired']);
        return back()->with('error', '⚠️ باقتك منتهية. <a href="' . route('packages') . '" style="color:#2563eb; font-weight:700;">ترقية الباقة</a>');
    }

    $package = \App\Models\Package::find($activePackage->package_id);
    $currentRequests = ServiceRequest::where('user_id', auth()->id())->count();

    if ($package->max_requests !== null && $currentRequests >= $package->max_requests) {
        return back()->with('error', '⚠️ لقد وصلت للحد الأقصى (' . $package->max_requests . ' طلبات) في باقتك "' . $package->name . '". <a href="' . route('packages') . '" style="color:#2563eb; font-weight:700;">ترقية إلى باقة أعلى</a>');
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'budget' => 'required|numeric|min:1',
    ]);
    
    ServiceRequest::create([
        'user_id' => auth()->id(),
        'title' => $request->title,
        'description' => $request->description,
        'budget' => $request->budget,
    ]);

    ActivityLogController::log(auth()->id(), 'create_request', 'إنشاء طلب خدمة');
    ActivityLogController::notifyAdmin(auth()->user()->name . ' نشر طلب خدمة: ' . $request->title, route('admin.reports'));
    
    return redirect()->route('home')->with('success', '✅ تم نشر الطلب بنجاح!');
}
    
    public function index()
    {
        $requests = ServiceRequest::with('user')->where('status', 'open')->latest()->get();
        return view('requests-index', compact('requests'));
    }
    
    public function show($id)
    {
        $serviceRequest = ServiceRequest::with('user')->findOrFail($id);
        $offers = Offer::where('request_id', $id)->with('provider')->latest()->get();
        $isOwner = (auth()->id() == $serviceRequest->user_id);
        $acceptedOffer = Offer::where('request_id', $id)->where('status', 'accepted')->first();
        
        return view('request-show', compact('serviceRequest', 'offers', 'isOwner', 'acceptedOffer'));
    }
    
    public function submitOffer(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:1',
        ]);
        
        Offer::create([
            'request_id' => $id,
            'provider_id' => auth()->id(),
            'price' => $request->price,
            'message' => $request->message,
        ]);
        
        $serviceRequest = ServiceRequest::find($id);
        Notification::create([
            'user_id' => $serviceRequest->user_id,
            'sender_id' => auth()->id(),
            'type' => 'offer_new',
            'message' => auth()->user()->name . ' قدم عرضًا على طلبك.',
            'link' => route('request.show', $id)
        ]);

        ActivityLogController::log(auth()->id(), 'submit_offer', 'تقديم عرض على طلب');
        ActivityLogController::notifyAdmin(auth()->user()->name . ' قدم عرض على طلب', route('admin.reports'));
        
        return back()->with('success', 'تم تقديم العرض!');
    }
    
    public function acceptOffer($offerId)
    {
        $offer = Offer::with('request')->findOrFail($offerId);
        
        if ($offer->request->user_id != auth()->id()) {
            return back()->with('error', 'غير مصرح لك.');
        }
        
        $offer->update(['status' => 'accepted']);
        Offer::where('request_id', $offer->request_id)->where('id', '!=', $offerId)->update(['status' => 'rejected']);
        $offer->request->update(['status' => 'in_progress']);
        
        Notification::create([
            'user_id' => $offer->provider_id,
            'sender_id' => auth()->id(),
            'type' => 'offer_accept',
            'message' => auth()->user()->name . ' قبل عرضك.',
            'link' => route('request.show', $offer->request_id)
        ]);

        ActivityLogController::log(auth()->id(), 'accept_offer', 'قبول عرض');
        
        return back()->with('success', 'تم قبول العرض!');
    }
    
    // ✅ إكمال الطلب فقط - بدون تقييم
    public function completeAndRate(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);
        
        if ($serviceRequest->user_id != auth()->id()) {
            return back()->with('error', 'غير مصرح لك.');
        }
        
        $serviceRequest->update(['status' => 'completed']);
        
        $acceptedOffer = Offer::where('request_id', $id)->where('status', 'accepted')->first();
        if ($acceptedOffer) {
            Notification::create([
                'user_id' => $acceptedOffer->provider_id,
                'sender_id' => auth()->id(),
                'type' => 'request_completed',
                'message' => '✅ تم إكمال الطلب: ' . $serviceRequest->title,
                'link' => route('request.show', $id)
            ]);
        }

        ActivityLogController::log(auth()->id(), 'complete_request', 'إكمال الطلب');
        
        return back()->with('success', 'تم إكمال الطلب بنجاح!');
    }
}