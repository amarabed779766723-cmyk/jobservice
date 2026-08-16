<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    // جلب جميع الخدمات مع البيانات المرتبطة
    public function index()
    {
        $services = Service::with(['provider'])
            ->latest()
            ->paginate(10);  // 10 خدمات لكل صفحة

        return response()->json([
            'success' => true,
            'data' => $services->items(),
            'total' => $services->total(),
            'current_page' => $services->currentPage(),
            'last_page' => $services->lastPage(),
        ]);
    }

    // جلب خدمة محددة
    public function show($id)
    {
        $service = Service::with(['provider', 'ratings'])->find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'الخدمة غير موجودة'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    // جلب التصنيفات مع أعداد الخدمات
    public function categories()
    {
        // تصنيفات ثابتة مع أعداد حقيقية من قاعدة البيانات
        $categories = [
            [
                'id' => 1,
                'name' => 'برمجة وتقنية',
                'icon' => '💻',
                'color' => '#4F46E5',
                'count' => Service::where('category_id', 1)->count()
            ],
            [
                'id' => 2,
                'name' => 'تصميم وإبداع',
                'icon' => '🎨',
                'color' => '#7C3AED',
                'count' => Service::where('category_id', 2)->count()
            ],
            [
                'id' => 3,
                'name' => 'الهندسة',
                'icon' => '⚙️',
                'color' => '#0D9488',
                'count' => Service::where('category_id', 3)->count()
            ],
            [
                'id' => 4,
                'name' => 'الصيانة والتشغيل',
                'icon' => '🔧',
                'color' => '#F59E0B',
                'count' => Service::where('category_id', 4)->count()
            ],
            [
                'id' => 5,
                'name' => 'الحرف اليدوية',
                'icon' => '🧵',
                'color' => '#EF4444',
                'count' => Service::where('category_id', 5)->count()
            ],
            [
                'id' => 6,
                'name' => 'التعليم والتدريب',
                'icon' => '📚',
                'color' => '#8B5CF6',
                'count' => Service::where('category_id', 6)->count()
            ],
            [
                'id' => 7,
                'name' => 'التسويق والمبيعات',
                'icon' => '📈',
                'color' => '#06B6D4',
                'count' => Service::where('category_id', 7)->count()
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
    public function nearby(Request $request)
{
    $lat = $request->lat;
    $lng = $request->lng;
    
    if (!$lat || !$lng) {
        return response()->json(['success' => false, 'data' => []]);
    }
    
    $services = Service::with('provider')
        ->select('*')
        ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$lat, $lng, $lat])
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->orderBy('distance')
        ->limit(6)
        ->get();
    
    return response()->json([
        'success' => true,
        'data' => $services
    ]);
}
}