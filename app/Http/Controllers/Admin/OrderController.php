<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'items.variant']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'payment_status' => 'required|string',
        ]);

        $order->update($request->only('status', 'payment_status'));

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng thành công.');
    }
}
