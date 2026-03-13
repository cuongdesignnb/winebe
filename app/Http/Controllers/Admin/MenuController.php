<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $menus = Menu::withCount('allItems')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Menus/Index', [
            'menus' => $menus,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Menus/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:menus,code',
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Menu::create([
            'name' => $validated['name'],
            'code' => Str::slug($validated['code']),
            'location' => $validated['location'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.menus.index')->with('success', 'Menu created.');
    }

    public function edit(Menu $menu)
    {
        $menu->load(['allItems' => fn($q) => $q->orderBy('sort_order')]);
        // Build tree in PHP for simplicity
        $items = $menu->allItems;
        $tree = $this->buildTree($items);

        return Inertia::render('Admin/Menus/Form', [
            'menu' => $menu,
            'menuItems' => $tree,
        ]);
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:menus,code,' . $menu->id,
            'location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $menu->update([
            'name' => $validated['name'],
            'code' => Str::slug($validated['code']),
            'location' => $validated['location'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.menus.index')->with('success', 'Menu updated.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted.');
    }

    // ---- Menu Item API endpoints ----

    public function storeItem(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'item_type' => 'required|string|in:custom_link,category,brand,page',
            'url' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:menu_items,id',
            'is_visible' => 'boolean',
            'open_in_new_tab' => 'boolean',
            'badge_text' => 'nullable|string|max:50',
            'icon_name' => 'nullable|string|max:100',
            'css_class' => 'nullable|string|max:255',
        ]);

        $maxSort = $menu->allItems()->where('parent_id', $validated['parent_id'] ?? null)->max('sort_order') ?? 0;

        $item = $menu->allItems()->create([
            'title' => $validated['title'],
            'item_type' => $validated['item_type'],
            'url' => $validated['url'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'is_visible' => $validated['is_visible'] ?? true,
            'open_in_new_tab' => $validated['open_in_new_tab'] ?? false,
            'badge_text' => $validated['badge_text'] ?? null,
            'icon_name' => $validated['icon_name'] ?? null,
            'css_class' => $validated['css_class'] ?? null,
            'sort_order' => $maxSort + 1,
            'level' => $validated['parent_id'] ? (MenuItem::find($validated['parent_id'])->level + 1) : 0,
        ]);

        return response()->json($item);
    }

    public function updateItem(Request $request, Menu $menu, MenuItem $item)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'item_type' => 'required|string|in:custom_link,category,brand,page',
            'url' => 'nullable|string|max:500',
            'is_visible' => 'boolean',
            'open_in_new_tab' => 'boolean',
            'badge_text' => 'nullable|string|max:50',
            'icon_name' => 'nullable|string|max:100',
            'css_class' => 'nullable|string|max:255',
        ]);

        $item->update($validated);
        return response()->json($item->fresh());
    }

    public function destroyItem(Menu $menu, MenuItem $item)
    {
        $item->delete();
        return response()->json(['success' => true]);
    }

    public function reorderItems(Request $request, Menu $menu)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.sort_order' => 'required|integer',
            'items.*.parent_id' => 'nullable|exists:menu_items,id',
        ]);

        foreach ($request->items as $data) {
            MenuItem::where('id', $data['id'])->update([
                'sort_order' => $data['sort_order'],
                'parent_id' => $data['parent_id'] ?? null,
            ]);
        }

        return response()->json(['success' => true]);
    }

    private function buildTree($items, $parentId = null)
    {
        $tree = [];
        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildTree($items, $item->id);
                $item->children_list = $children;
                $tree[] = $item;
            }
        }
        return $tree;
    }
}
