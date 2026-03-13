<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distillery;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DistilleryController extends Controller
{
    public function index(Request $request)
    {
        $distilleries = Distillery::with('region')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Distilleries/Index', [
            'distilleries' => $distilleries,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Distilleries/Form', [
            'regions' => Region::orderBy('name')->get(['id', 'name'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'region_id' => 'nullable|exists:regions,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Distillery::create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->route('admin.distilleries.index')->with('success', 'Đã tạo nhà chưng cất thành công.');
    }

    public function edit(Distillery $distillery)
    {
        return Inertia::render('Admin/Distilleries/Form', [
            'distillery' => $distillery,
            'regions' => Region::orderBy('name')->get(['id', 'name'])
        ]);
    }

    public function update(Request $request, Distillery $distillery)
    {
        $validated = $request->validate([
            'region_id' => 'nullable|exists:regions,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $distillery->update($validated);

        return redirect()->route('admin.distilleries.index')->with('success', 'Đã cập nhật nhà chưng cất thành công.');
    }

    public function destroy(Distillery $distillery)
    {
        $distillery->delete();
        return redirect()->route('admin.distilleries.index')->with('success', 'Đã xóa nhà chưng cất thành công.');
    }
}
