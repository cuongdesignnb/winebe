<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Taxon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'products' => Product::count(),
                'orders' => \App\Models\Order::count(),
                'inquiries' => \App\Models\Inquiry::count(),
                'published' => Product::where('is_published', true)->count(),
            ],
        ]);
    }
}
