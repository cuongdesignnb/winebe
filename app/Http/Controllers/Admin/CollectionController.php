<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $collections = Collection::when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->withCount('products')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Collections/Index', [
            'collections' => $collections,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Collections/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        Collection::create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.collections.index')->with('success', 'Đã tạo bộ sưu tập thành công.');
    }

    public function edit(Collection $collection)
    {
        return Inertia::render('Admin/Collections/Form', [
            'collection' => $collection,
        ]);
    }

    public function update(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $collection->update([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.collections.index')->with('success', 'Đã cập nhật bộ sưu tập thành công.');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();
        return redirect()->route('admin.collections.index')->with('success', 'Đã xóa bộ sưu tập thành công.');
    }
}
