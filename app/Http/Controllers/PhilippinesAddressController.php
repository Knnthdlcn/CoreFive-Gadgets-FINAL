<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PhilippinesAddressController extends Controller
{
    public function regions(): JsonResponse
    {
        $items = Cache::remember('ph:regions', now()->addHours(12), function () {
            return DB::table('philippine_regions')
                ->select(['region_code as code', 'name'])
                ->orderBy('name')
                ->get();
        });

        return response()->json($items);
    }

    public function provinces(string $regionCode): JsonResponse
    {
        $cacheKey = 'ph:provinces:' . $regionCode;

        $items = Cache::remember($cacheKey, now()->addHours(12), function () use ($regionCode) {
            return DB::table('philippine_provinces')
                ->where('region_code', $regionCode)
                ->select(['province_code as code', 'name'])
                ->get();
        });

        return response()->json($items);
    }

    public function cities(string $provinceCode): JsonResponse
    {
        $cacheKey = 'ph:cities:' . $provinceCode;

        $items = Cache::remember($cacheKey, now()->addHours(12), function () use ($provinceCode) {
            return DB::table('philippine_cities')
                ->where('province_code', $provinceCode)
                ->select(['city_code as code', 'name'])
                ->orderBy('name')
                ->get();
        });

        return response()->json($items);
    }

    public function barangays(string $cityCode): JsonResponse
    {
        $cacheKey = 'ph:barangays:' . $cityCode;

        $items = Cache::remember($cacheKey, now()->addHours(12), function () use ($cityCode) {
            return DB::table('philippine_barangays')
                ->where('city_code', $cityCode)
                ->select(['psgc_code as code', 'name'])
                ->orderBy('name')
                ->get();
        });

        return response()->json($items);
    }
}
