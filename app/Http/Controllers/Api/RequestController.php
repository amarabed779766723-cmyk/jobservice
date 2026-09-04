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

        // ✅ تحويل الهاشتاقات
        $requests->getCollection()->transform(function($request) {
            if ($request->description) {
                $request->description = preg_replace(
                    '/#([\p{Arabic}a-zA-Z0-9_]+)/u',
                    '<a href="/hashtag/$1" style="color:#2563EB; text-decoration:none; font-weight:600;">#$1</a>',
                    $request->description
                );
            }
            return $request;
        });

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

        // ✅ تحويل الهاشتاقات
        if ($request->description) {
            $request->description = preg_replace(
                '/#([\p{Arabic}a-zA-Z0-9_]+)/u',
                '<a href="/hashtag/$1" style="color:#2563EB; text-decoration:none; font-weight:600;">#$1</a>',
                $request->description
            );
        }

        return response()->json([
            'success' => true,
            'data' => $request
        ]);
    }
}