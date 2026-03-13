<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Taxon;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Taxon::where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_published', true)])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($cat) {
                $image = $cat->mediaLinks()->with('asset')->first();
                $cat->image = $image && $image->asset ? '/storage/' . $image->asset->path : null;
                return $cat;
            });

        return response()->json(['data' => $categories]);
    }

    public function show(Request $request, $slug)
    {
        $category = Taxon::where('slug', $slug)->firstOrFail();

        $limit = $request->query('limit', 12);
        $products = $category->products()->with(['brand', 'country', 'region', 'variants'])
            ->where('is_published', true)
            ->paginate($limit);

        // Attach images
        $products->getCollection()->transform(function ($product) {
            $link = $product->mediaLinks()->with('asset')->first();
            $product->primary_image = ($link && $link->asset) ? '/storage/' . $link->asset->path : null;
            return $product;
        });

        return response()->json([
            'category' => $category,
            'products' => $products
        ]);
    }
}
