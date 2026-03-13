<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CmsPageController extends Controller
{
    public function index(Request $request)
    {
        $pages = CmsPage::when($request->search, fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->type, fn($q, $t) => $q->where('page_type', $t))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Pages/Index', [
            'pages' => $pages,
            'filters' => $request->only('search', 'status', 'type'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Pages/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'page_type' => 'required|string|in:static,blog,landing,faq',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug',
            'status' => 'required|string|in:draft,published,archived',
            'excerpt' => 'nullable|string|max:500',
            'body_html' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'published_at' => 'nullable|date',
            'media_id' => 'nullable|exists:media_assets,id',
        ]);

        $page = CmsPage::create([
            'title' => $validated['title'],
            'slug' => !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']),
            'page_type' => $validated['page_type'],
            'status' => $validated['status'],
            'excerpt' => $validated['excerpt'] ?? null,
            'body_html' => $validated['body_html'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'published_at' => $validated['status'] === 'published' ? ($validated['published_at'] ?? now()) : null,
        ]);

        if (!empty($validated['media_id'])) {
            $page->attachMedia([$validated['media_id']], 'featured_image');
        }

        return redirect()->route('admin.pages.index')->with('success', 'Đã tạo trang thành công.');
    }

    public function edit(CmsPage $page)
    {
        $page->media_id = $page->mediaLinks()->where('collection_name', 'featured_image')->first()?->asset_id;
        return Inertia::render('Admin/Pages/Form', [
            'page' => $page,
        ]);
    }

    public function update(Request $request, CmsPage $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'page_type' => 'required|string|in:static,blog,landing,faq',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug,' . $page->id,
            'status' => 'required|string|in:draft,published,archived',
            'excerpt' => 'nullable|string|max:500',
            'body_html' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'published_at' => 'nullable|date',
            'media_id' => 'nullable|exists:media_assets,id',
        ]);

        $page->update([
            'title' => $validated['title'],
            'slug' => !empty($validated['slug']) ? Str::slug($validated['slug']) : $page->slug,
            'page_type' => $validated['page_type'],
            'status' => $validated['status'],
            'excerpt' => $validated['excerpt'] ?? null,
            'body_html' => $validated['body_html'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'published_at' => $validated['status'] === 'published'
                ? ($validated['published_at'] ?? $page->published_at ?? now())
                : null,
        ]);

        if (array_key_exists('media_id', $validated)) {
            if (empty($validated['media_id'])) {
                $page->mediaLinks()->where('collection_name', 'featured_image')->delete();
            } else {
                $page->attachMedia([$validated['media_id']], 'featured_image');
            }
        }

        return redirect()->route('admin.pages.index')->with('success', 'Đã cập nhật trang thành công.');
    }

    public function destroy(CmsPage $page)
    {
        $page->delete();
        return redirect()->route('admin.pages.index')->with('success', 'Đã xóa trang thành công.');
    }
}
