<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InquiryController extends Controller
{
    public function index()
    {
        $inquiries = Inquiry::with('product')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Inquiries/Index', [
            'inquiries' => $inquiries
        ]);
    }

    public function update(Request $request, Inquiry $inquiry)
    {
        $request->validate([
            'status' => 'required|string',
            'admin_note' => 'nullable|string',
        ]);

        $inquiry->update($request->only('status', 'admin_note'));

        return back()->with('success', 'Đã cập nhật trạng thái yêu cầu tư vấn.');
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();
        return back()->with('success', 'Đã xóa yêu cầu tư vấn thành công.');
    }
}
