<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'reported_user_id' => 'nullable|integer|exists:users,id',
            'service_id' => 'nullable|integer|exists:services,id',
            'post_id' => 'nullable|integer|exists:posts,id',
        ]);

        Report::create([
            'reporter_id' => Auth::id(),
            'reported_user_id' => $request->reported_user_id,
            'service_id' => $request->service_id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        ActivityLogController::log(Auth::id(), 'report', 'إرسال بلاغ');
        ActivityLogController::notifyAdmin('بلاغ جديد من ' . Auth::user()->name, route('admin.reports'));

        return back()->with('success', 'تم إرسال البلاغ بنجاح. سنراجعه قريباً.');
    }
}