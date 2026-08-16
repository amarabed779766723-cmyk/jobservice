<?php

namespace App\Http\Controllers\Api;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RequestController extends Controller
{
    // جلب الطلبات المفتوحة
    public function index()
    {
        $requests = ServiceRequest::with(['user'])
            ->where('status', 'open')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $requests->items(),
            'total' => $requests->total(),
            'current_page' => $requests->currentPage(),
            'last_page' => $requests->lastPage(),
        ]);
    }

    // جلب طلب محدد
    public function show($id)
    {
        $request = ServiceRequest::with(['user', 'offers'])->find($id);

        if (!$request) {
            return response()->json([
                'success' => false,
                'message' => 'الطلب غير موجود'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $request
        ]);
    }
}