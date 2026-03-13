<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $brands = Brand::when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->withCount('products')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Brands/Index', [
            'brands' => $brands,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Brands/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'story_html' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'media_id' => 'nullable|exists:media_assets,id',
        ]);

        $brand = Brand::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'short_description' => $validated['short_description'] ?? null,
            'story_html' => $validated['story_html'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'status' => $validated['status'],
        ]);

        if (!empty($validated['media_id'])) {
            $brand->attachMedia([$validated['media_id']], 'logo');
        }

        return redirect()->route('admin.brands.index')->with('success', 'Đã tạo thương hiệu thành công.');
    }

    public function edit(Brand $brand)
    {
        $brand->media_id = $brand->getFirstMediaUrl('logo') ? $brand->mediaLinks()->where('collection_name', 'logo')->first()?->asset_id : null;
        return Inertia::render('Admin/Brands/Form', ['brand' => $brand]);
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'story_html' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'media_id' => 'nullable|exists:media_assets,id',
        ]);

        $brand->update([
            'name' => $validated['name'],
            'short_description' => $validated['short_description'] ?? null,
            'story_html' => $validated['story_html'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'status' => $validated['status'],
        ]);

        if (array_key_exists('media_id', $validated)) {
            if (empty($validated['media_id'])) {
                $brand->mediaLinks()->where('collection_name', 'logo')->delete();
            } else {
                $brand->attachMedia([$validated['media_id']], 'logo');
            }
        }

        return redirect()->route('admin.brands.index')->with('success', 'Đã cập nhật thương hiệu thành công.');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Đã xóa thương hiệu thành công.');
    }
}
