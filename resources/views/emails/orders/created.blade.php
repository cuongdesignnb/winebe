<x-mail::message>
    # Cảm ơn bạn đã đặt hàng!

    Chào {{ $order->customer_name }},

    Chúng tôi đã nhận được đơn hàng **#{{ $order->order_number }}** của bạn. Cửa hàng sẽ sớm liên hệ để xác nhận và giao
    hàng.

    **Chi tiết đơn hàng:**
    - Tổng tiền: {{ number_format($order->total_amount, 0, ',', '.') }}đ
    - Hình thức thanh toán: {{ $order->payment_method }}
    - Địa chỉ: {{ $order->shipping_address }}

    <x-mail::button :url="config('app.url') . '/tai-khoan/don-hang/' . $order->id">
        Xem chi tiết đơn hàng
    </x-mail::button>

    Trân trọng,<br>
    {{ config('app.name') }}
</x-mail::message>