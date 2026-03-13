<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->query('limit', 12);

        $query = Product::with(['brand', 'country', 'region', 'variants'])
            ->where('is_published', true);

        // Sorting
        $sort = $request->query('sort', '-created_at');
        if ($sort === '-created_at')
            $query->latest();
        elseif ($sort === '-price')
            $query->orderByDesc(
                \App\Models\ProductVariant::select('price')->whereColumn('product_id', 'products.id')->limit(1)
            );
        elseif ($sort === 'price')
            $query->orderBy(
                \App\Models\ProductVariant::select('price')->whereColumn('product_id', 'products.id')->limit(1)
            );
        elseif ($sort === 'name')
            $query->orderBy('name');

        // Filters
        if ($request->filled('brand_id'))
            $query->where('brand_id', $request->brand_id);
        if ($request->filled('country_id'))
            $query->where('country_id', $request->country_id);
        if ($request->filled('product_type'))
            $query->where('product_type', $request->product_type);
        if ($request->filled('search'))
            $query->where('name', 'like', '%' . $request->search . '%');

        $products = $query->paginate($limit);

        // Attach primary image from media
        $products->getCollection()->transform(function ($product) {
            $product->primary_image = $this->getProductImage($product);
            return $product;
        });

        return response()->json($products);
    }

    public function show($slug)
    {
        $product = Product::with(['brand', 'country', 'region', 'variants', 'tastingNotes', 'attributes'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $data = $product->toArray();
        $data['primary_image'] = $this->getProductImage($product);
        $data['images'] = $this->getProductImages($product);
        $data['tasting_notes'] = $product->tastingNotes;

        return response()->json($data);
    }

    public function featured()
    {
        $products = Product::with(['brand', 'country', 'region', 'variants'])
            ->where('is_published', true)
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($product) {
                $product->primary_image = $this->getProductImage($product);
                return $product;
            });

        return response()->json(['data' => $products]);
    }

    private function getProductImage($product)
    {
        $link = $product->mediaLinks()->with('asset')->first();
        if ($link && $link->asset) {
            return $link->asset->url;
        }
        return null;
    }

    private function getProductImages($product)
    {
        return $product->mediaLinks()->with('asset')->get()->map(function ($link) {
            return $link->asset ? $link->asset->url : null;
        })->filter()->values()->toArray();
    }
}
