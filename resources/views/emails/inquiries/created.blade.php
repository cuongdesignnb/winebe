<x-mail::message>
    # Yêu cầu tư vấn sản phẩm mới

    Chào Admin,

    Có một yêu cầu tư vấn mới cho sản phẩm **{{ $inquiry->product?->name }}**.

    **Thông tin khách hàng:**
    - Họ tên: {{ $inquiry->name }}
    - Điện thoại: {{ $inquiry->phone }}
    - Email: {{ $inquiry->email }}

    **Lời nhắn:**
    {{ $inquiry->message }}

    <x-mail::button :url="config('app.url') . '/admin/inquiries'">
        Quản lý yêu cầu
    </x-mail::button>

    Trân trọng,<br>
    {{ config('app.name') }}
</x-mail::message>