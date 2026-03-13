<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'nullable|string',
        ]);

        $inquiry = Inquiry::create($validated);

        return response()->json([
            'message' => 'Yêu cầu của bạn đã được gửi thành công. Chúng tôi sẽ liên hệ lại sớm.',
            'data' => $inquiry
        ], 201);
    }
}
