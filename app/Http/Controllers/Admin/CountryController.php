<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->withCount('regions')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Countries/Index', [
            'countries' => $countries,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Countries/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => 'nullable|string|max:2',
        ]);

        Country::create([
            ...$validated,
            'slug' => Str::slug($validated['name']),
            'iso_code' => strtoupper($validated['iso_code']),
        ]);

        return redirect()->route('admin.countries.index')->with('success', 'Đã tạo quốc gia thành công.');
    }

    public function edit(Country $country)
    {
        return Inertia::render('Admin/Countries/Form', ['country' => $country]);
    }

    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => 'nullable|string|max:2',
        ]);

        $country->update([
            ...$validated,
            'iso_code' => strtoupper($validated['iso_code']),
        ]);

        return redirect()->route('admin.countries.index')->with('success', 'Đã cập nhật quốc gia thành công.');
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return redirect()->route('admin.countries.index')->with('success', 'Đã xóa quốc gia thành công.');
    }
}
