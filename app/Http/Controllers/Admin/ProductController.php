<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Country;
use App\Models\Taxon;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['brand', 'country', 'variants'])
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->brand_id, fn($q, $b) => $q->where('brand_id', $b))
            ->when($request->country_id, fn($q, $c) => $q->where('country_id', $c))
            ->when($request->is_published !== null, fn($q) => $q->where('is_published', $request->boolean('is_published')))
            ->when($request->taxon_id, function ($q, $tid) {
                $q->whereHas('taxons', fn($t) => $t->where('taxa.id', $tid));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => $request->only('search', 'brand_id', 'country_id', 'taxon_id', 'is_published'),
            'brands' => Brand::orderBy('name')->get(['id', 'name']),
            'countries' => Country::orderBy('name')->get(['id', 'name']),
            'categories' => Taxon::orderBy('name')->get(['id', 'name', 'taxon_type']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Form', [
            'brands' => Brand::orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
            'categories' => Taxon::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_type' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
            'country_id' => 'nullable|exists:countries,id',
            'sale_mode' => 'required|in:online_checkout,inquiry_only,showroom_only',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'short_desc' => 'nullable|string',
            'long_desc_html' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'volume_ml' => 'nullable|integer',
            'abv_percent' => 'nullable|numeric',
            'sku' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'taxon_ids' => 'nullable|array',
            'taxon_ids.*' => 'exists:taxa,id',
            'media_ids' => 'nullable|array',
            'media_ids.*' => 'exists:media_assets,id',
            'attributes' => 'nullable|array',
            'attributes.*.attr_key' => 'required|string',
            'attributes.*.attr_value' => 'nullable|string',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'product_type' => $validated['product_type'] ?? null,
            'brand_id' => $validated['brand_id'] ?? null,
            'country_id' => $validated['country_id'] ?? null,
            'sale_mode' => $validated['sale_mode'],
            'is_published' => $validated['is_published'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
            'short_desc' => $validated['short_desc'] ?? null,
            'long_desc_html' => $validated['long_desc_html'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
        ]);

        if (!empty($validated['price']) || !empty($validated['sku'])) {
            $product->variants()->create([
                'price' => $validated['price'] ?? 0,
                'volume_ml' => $validated['volume_ml'] ?? null,
                'abv_percent' => $validated['abv_percent'] ?? null,
                'sku' => $validated['sku'] ?? strtoupper(Str::random(8)),
            ]);
        }

        if (isset($validated['taxon_ids'])) {
            $product->taxons()->sync($validated['taxon_ids']);
        }

        if (isset($validated['media_ids'])) {
            $product->attachMedia($validated['media_ids'], 'images');
        }

        if (isset($validated['attributes'])) {
            foreach ($validated['attributes'] as $attr) {
                $product->attributes()->create($attr);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Đã tạo sản phẩm thành công.');
    }

    public function edit(Product $product)
    {
        $product->load(['variants', 'brand', 'country', 'taxons', 'mediaAssets', 'attributes']);
        $product->media_ids = $product->mediaAssets->pluck('id')->toArray();

        return Inertia::render('Admin/Products/Form', [
            'product' => $product,
            'brands' => Brand::orderBy('name')->get(),
            'countries' => Country::orderBy('name')->get(),
            'categories' => Taxon::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_type' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
            'country_id' => 'nullable|exists:countries,id',
            'sale_mode' => 'required|in:online_checkout,inquiry_only,showroom_only',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'short_desc' => 'nullable|string',
            'long_desc_html' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'volume_ml' => 'nullable|integer',
            'abv_percent' => 'nullable|numeric',
            'sku' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'taxon_ids' => 'nullable|array',
            'taxon_ids.*' => 'exists:taxa,id',
            'media_ids' => 'nullable|array',
            'media_ids.*' => 'exists:media_assets,id',
            'attributes' => 'nullable|array',
            'attributes.*.attr_key' => 'required|string',
            'attributes.*.attr_value' => 'nullable|string',
        ]);

        $product->update([
            'name' => $validated['name'],
            'product_type' => $validated['product_type'] ?? null,
            'brand_id' => $validated['brand_id'] ?? null,
            'country_id' => $validated['country_id'] ?? null,
            'sale_mode' => $validated['sale_mode'],
            'is_published' => $validated['is_published'] ?? true,
            'is_featured' => $validated['is_featured'] ?? false,
            'short_desc' => $validated['short_desc'] ?? null,
            'long_desc_html' => $validated['long_desc_html'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
        ]);

        $variant = $product->variants()->first();
        if ($variant instanceof ProductVariant) {
            $variant->update([
                'price' => $validated['price'] ?? $variant->price,
                'volume_ml' => $validated['volume_ml'] ?? $variant->volume_ml,
                'abv_percent' => $validated['abv_percent'] ?? $variant->abv_percent,
                'sku' => $validated['sku'] ?? $variant->sku,
            ]);
        }

        if (isset($validated['taxon_ids'])) {
            $product->taxons()->sync($validated['taxon_ids']);
        }

        if (isset($validated['media_ids'])) {
            $product->attachMedia($validated['media_ids'], 'images');
        }

        if (isset($validated['attributes'])) {
            $product->attributes()->delete();
            foreach ($validated['attributes'] as $attr) {
                $product->attributes()->create($attr);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Đã cập nhật sản phẩm thành công.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm thành công.');
    }
}
