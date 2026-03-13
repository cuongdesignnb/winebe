<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RegionController extends Controller
{
    public function index(Request $request)
    {
        $regions = Region::with('country')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->withCount('distilleries')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Regions/Index', [
            'regions' => $regions,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Regions/Form', [
            'countries' => Country::orderBy('name')->get(['id', 'name'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Region::create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.regions.index')->with('success', 'Đã tạo vùng miền thành công.');
    }

    public function edit(Region $region)
    {
        return Inertia::render('Admin/Regions/Form', [
            'region' => $region,
            'countries' => Country::orderBy('name')->get(['id', 'name'])
        ]);
    }

    public function update(Request $request, Region $region)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $region->update($validated);

        return redirect()->route('admin.regions.index')->with('success', 'Đã cập nhật vùng miền thành công.');
    }

    public function destroy(Region $region)
    {
        $region->delete();
        return redirect()->route('admin.regions.index')->with('success', 'Đã xóa vùng miền thành công.');
    }
}
