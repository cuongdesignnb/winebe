<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taxon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Taxon::query()
            ->withCount('products')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->type, fn($q, $t) => $q->where('taxon_type', $t))
            ->with('parent')
            ->orderBy('taxon_type')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        $parents = Taxon::whereNull('parent_id')->get();
        return Inertia::render('Admin/Categories/Form', ['parents' => $parents]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:taxa,id',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
            'taxon_type' => 'required|string|in:category,filter',
            'media_id' => 'nullable|exists:media_assets,id',
        ]);

        $category = Taxon::create([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'slug' => Str::slug($validated['name']),
            'taxon_type' => $validated['taxon_type'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (!empty($validated['media_id'])) {
            $category->attachMedia([$validated['media_id']], 'thumbnail');
        }

        return redirect()->route('admin.categories.index')->with('success', 'Đã tạo danh mục thành công.');
    }

    public function edit(Taxon $category)
    {
        $parents = Taxon::whereNull('parent_id')->where('id', '!=', $category->id)->get();
        $category->media_id = $category->mediaLinks()->where('collection_name', 'thumbnail')->first()?->asset_id;
        return Inertia::render('Admin/Categories/Form', ['category' => $category, 'parents' => $parents]);
    }

    public function update(Request $request, Taxon $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:taxa,id',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
            'taxon_type' => 'required|string|in:category,filter',
            'media_id' => 'nullable|exists:media_assets,id',
        ]);

        $category->update([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'taxon_type' => $validated['taxon_type'] ?? $category->taxon_type,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (array_key_exists('media_id', $validated)) {
            if (empty($validated['media_id'])) {
                $category->mediaLinks()->where('collection_name', 'thumbnail')->delete();
            } else {
                $category->attachMedia([$validated['media_id']], 'thumbnail');
            }
        }

        return redirect()->route('admin.categories.index')->with('success', 'Đã cập nhật danh mục thành công.');
    }

    public function destroy(Taxon $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục thành công.');
    }
}
