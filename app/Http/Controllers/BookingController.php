<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Notification;
use App\Models\WorkDelivery;
use App\Models\Rating;

class BookingController extends Controller
{
    public function create($serviceId)
    {
        $service = Service::with('provider')->findOrFail($serviceId);
        return view('book', compact('service'));
    }

    public function store(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);
        
        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
        ]);
        
        $booking = Booking::create([
            'service_id' => $service->id,
            'client_id' => Auth::id(),
            'provider_id' => $service->provider_id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);
        
        if ($service->provider_id != Auth::id()) {
            Notification::create([
                'user_id' => $service->provider_id,
                'sender_id' => Auth::id(),
                'type' => 'booking_new',
                'message' => Auth::user()->name . ' حجز خدمتك "' . $service->title . '" بتاريخ ' . $request->booking_date,
                'link' => route('bookings.index')
            ]);
        }
        
        return redirect()->route('bookings.index')->with('success', 'تم إرسال طلب الحجز بنجاح');
    }

    public function index()
    {
        $myBookings = Booking::with('service', 'provider')
            ->where('client_id', Auth::id())
            ->orderBy('booking_date', 'desc')
            ->get();
            
        $providerBookings = collect();
        if (Auth::user()->user_type === 'provider') {
            $providerBookings = Booking::with('service', 'client')
                ->where('provider_id', Auth::id())
                ->orderBy('booking_date', 'desc')
                ->get();
        }
        
        return view('my-bookings', compact('myBookings', 'providerBookings'));
    }

    public function cancel($id)
    {
        $booking = Booking::where('id', $id)->where('client_id', Auth::id())->firstOrFail();
        $booking->update(['status' => 'cancelled']);
        
        Notification::create([
            'user_id' => $booking->provider_id,
            'sender_id' => Auth::id(),
            'type' => 'booking_cancelled',
            'message' => Auth::user()->name . ' ألغى الحجز بتاريخ ' . $booking->booking_date,
            'link' => route('bookings.index')
        ]);
        
        return redirect()->route('bookings.index')->with('success', 'تم إلغاء الحجز');
    }
    
    public function approve($id)
    {
        $booking = Booking::where('id', $id)->where('provider_id', Auth::id())->firstOrFail();
        $booking->update(['status' => 'approved', 'provider_reply' => 'تم تأكيد الحجز']);
        
        Notification::create([
            'user_id' => $booking->client_id,
            'sender_id' => Auth::id(),
            'type' => 'booking_approved',
            'message' => '✅ تم قبول حجزك لـ "' . $booking->service->title . '"',
            'link' => route('bookings.index')
        ]);
        
        return back()->with('success', 'تم قبول الحجز');
    }
    
    public function reject(Request $request, $id)
    {
        $booking = Booking::where('id', $id)->where('provider_id', Auth::id())->firstOrFail();
        $reply = $request->reply ?? 'الوقت غير مناسب';
        $suggestedDate = $request->suggested_date;
        $suggestedTime = $request->suggested_time;
        
        $booking->update([
            'status' => 'rejected',
            'provider_reply' => $reply,
            'suggested_date' => $suggestedDate,
            'suggested_time' => $suggestedTime,
        ]);
        
        $message = '❌ تم رفض حجزك لـ "' . $booking->service->title . '"';
        $message .= ' - السبب: ' . $reply;
        if ($suggestedDate) {
            $message .= ' | 📅 موعد بديل: ' . $suggestedDate . ' ' . $suggestedTime;
        }
        
        Notification::create([
            'user_id' => $booking->client_id,
            'sender_id' => Auth::id(),
            'type' => 'booking_rejected',
            'message' => $message,
            'link' => route('bookings.index')
        ]);
        
        return back()->with('success', 'تم رفض الحجز');
    }

    public function deliveryForm($id)
    {
        $booking = Booking::with('service')->findOrFail($id);
        return view('delivery.send', compact('booking'));
    }
    
    public function sendDelivery(Request $request, $id)
    {
        $booking = Booking::with('service')->findOrFail($id);
        
        $delivery = WorkDelivery::create([
            'booking_id' => $booking->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $booking->client_id,
            'title' => 'تسليم: ' . $booking->service->title,
            'description' => $request->description,
            'file_attachment' => $request->hasFile('file') ? $request->file('file')->store('deliveries', 'public') : null,
            'file_name' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : null,
            'status' => 'sent',
        ]);
        
        Notification::create([
            'user_id' => $booking->client_id,
            'sender_id' => Auth::id(),
            'type' => 'work_delivered',
            'message' => '📦 تم تسليم العمل: ' . $booking->service->title,
            'link' => route('bookings.index')
        ]);
        
        return redirect()->route('bookings.index')->with('success', 'تم إرسال العمل للعميل');
    }
    
    public function confirmDelivery($id)
    {
        $delivery = WorkDelivery::findOrFail($id);
        $delivery->update(['client_confirmed' => 1, 'status' => 'received']);
        
        return back()->with('success', 'تم تأكيد الاستلام. يمكنك الآن تقييم الخدمة.');
    }
    
    public function rateDelivery(Request $request, $id)
    {
        $delivery = WorkDelivery::findOrFail($id);
        
        Rating::create([
            'rater_id' => Auth::id(),
            'rated_user_id' => $delivery->sender_id,
            'score' => $request->score,
            'review' => $request->review,
        ]);
        
        $delivery->update(['status' => 'rated']);
        
        return back()->with('success', '✅ تم التقييم بنجاح!');
    }
    
}