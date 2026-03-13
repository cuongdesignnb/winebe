<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Brand;
use App\Models\Country;
use App\Models\Collection;

class MenuController extends Controller
{
    public function show($code)
    {
        $menu = Menu::where('code', $code)
            ->where('is_active', true)
            ->with([
                'items' => function ($q) {
                    $q->where('is_visible', true)
                        ->whereNull('parent_id')
                        ->orderBy('sort_order')
                        ->with([
                            'children' => function ($q) {
                                $q->where('is_visible', true)->orderBy('sort_order');
                            }
                        ]);
                }
            ])
            ->first();

        if (!$menu)
            return response()->json(['data' => null]);

        return response()->json(['data' => $menu]);
    }

    public function brands()
    {
        $brands = Brand::where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_published', true)])
            ->orderBy('name')
            ->get()
            ->map(function ($brand) {
                $link = $brand->mediaLinks()->with('asset')->first();
                $brand->logo = ($link && $link->asset) ? '/storage/' . $link->asset->path : null;
                return $brand;
            });

        return response()->json(['data' => $brands]);
    }

    public function countries()
    {
        $countries = Country::withCount(['products' => fn($q) => $q->where('is_published', true)])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $countries]);
    }

    public function collections()
    {
        $collections = Collection::where('is_active', true)
            ->get()
            ->map(function ($col) {
                $link = $col->mediaLinks()->with('asset')->first();
                $col->image = ($link && $link->asset) ? '/storage/' . $link->asset->path : null;
                return $col;
            });

        return response()->json(['data' => $collections]);
    }
}
