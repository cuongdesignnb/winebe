<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ShoppingBag, User, MapPin, CreditCard, Clock, Package, CheckCircle2, ChevronLeft } from 'lucide-vue-next';

const props = defineProps({
    order: Object
});

const updateStatus = (field, value) => {
    router.put(route('admin.orders.update', props.order.id), {
        [field]: value,
        // keep other field as is
        status: field === 'status' ? value : props.order.status,
        payment_status: field === 'payment_status' ? value : props.order.payment_status,
    });
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

const statusMap = {
    pending: { label: 'Chờ xử lý', color: 'bg-amber-100 text-amber-700 border-amber-200' },
    processing: { label: 'Đang đóng gói', color: 'bg-blue-100 text-blue-700 border-blue-200' },
    shipped: { label: 'Đang giao hàng', color: 'bg-purple-100 text-purple-700 border-purple-200' },
    delivered: { label: 'Đã giao hàng', color: 'bg-green-100 text-green-700 border-green-200' },
    cancelled: { label: 'Đã hủy', color: 'bg-red-100 text-red-700 border-red-200' },
};

const paymentStatusMap = {
    unpaid: { label: 'Chưa thanh toán', color: 'bg-gray-100 text-gray-600' },
    paid: { label: 'Đã thanh toán', color: 'bg-green-100 text-green-700' },
    refunded: { label: 'Đã hoàn tiền', color: 'bg-red-100 text-red-700' },
};
</script>

<template>
    <AdminLayout>
        <!-- Header -->
        <div class="mb-8">
            <Link :href="route('admin.orders.index')" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-900 transition-colors mb-4 group">
                <ChevronLeft class="w-4 h-4 mr-1 group-hover:-translate-x-1 transition-transform" />
                Quay lại danh sách đơn hàng
            </Link>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gray-900 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-gray-200">
                        <ShoppingBag class="w-7 h-7" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Đơn hàng #{{ order.order_number }}</h1>
                        <p class="text-sm text-gray-500 mt-1 flex items-center gap-2">
                            <Clock class="w-3.5 h-3.5" />
                            Ngày đặt: {{ new Date(order.created_at).toLocaleString('vi-VN') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <select 
                        @change="e => updateStatus('status', e.target.value)" 
                        :value="order.status"
                        class="bg-white border-gray-200 rounded-xl text-sm font-semibold px-4 py-2.5 focus:ring-2 focus:ring-amber-400 outline-none shadow-sm"
                    >
                        <option value="pending">Chờ xử lý</option>
                        <option value="processing">Đang đóng gói</option>
                        <option value="shipped">Đang giao hàng</option>
                        <option value="delivered">Đã giao hàng</option>
                        <option value="cancelled">Hủy đơn</option>
                    </select>
                    <button class="bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-amber-600 transition-all shadow-md">
                        In hóa đơn
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Order Items -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <Package class="w-5 h-5 text-amber-500" />
                            Danh sách sản phẩm
                        </h2>
                        <span class="text-sm font-semibold text-gray-500">{{ order.items.length }} sản phẩm</span>
                    </div>
                    <div class="p-8">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-4">
                                    <th class="pb-4">Sản phẩm</th>
                                    <th class="pb-4 text-center">Đơn giá</th>
                                    <th class="pb-4 text-center">Số lượng</th>
                                    <th class="pb-4 text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="item in order.items" :key="item.id" class="group">
                                    <td class="py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-center p-2 group-hover:bg-amber-50 group-hover:border-amber-100 transition-colors">
                                                <Wine class="w-8 h-8 text-gray-400 group-hover:text-amber-500 transition-colors" />
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 group-hover:text-amber-600 transition-colors">{{ item.product?.name }}</p>
                                                <p v-if="item.variant" class="text-xs text-gray-400 mt-1 font-medium italic">
                                                    Phân loại: {{ item.variant.volume_ml }}ml - {{ item.variant.abv_percent }}%
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 text-center text-sm font-medium text-gray-600">
                                        {{ formatPrice(item.price) }}
                                    </td>
                                    <td class="py-6 text-center text-sm font-bold text-gray-900">
                                        x {{ item.quantity }}
                                    </td>
                                    <td class="py-6 text-right text-sm font-bold text-gray-900">
                                        {{ formatPrice(item.price * item.quantity) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="mt-8 border-t border-gray-100 pt-8 flex flex-col items-end space-y-3">
                            <div class="flex justify-between w-full max-w-[280px] text-sm text-gray-500">
                                <span>Tạm tính:</span>
                                <span>{{ formatPrice(order.total_amount) }}</span>
                            </div>
                            <div class="flex justify-between w-full max-w-[280px] text-sm text-gray-500">
                                <span>Phí vận chuyển:</span>
                                <span class="text-green-600">Miễn phí</span>
                            </div>
                            <div class="flex justify-between w-full max-w-[280px] text-lg font-black text-gray-900 border-t border-gray-100 pt-3">
                                <span>Tổng cộng:</span>
                                <span class="text-amber-600">{{ formatPrice(order.total_amount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logistics Timeline (Draft) -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 font-serif">Trạng thái vận chuyển</h2>
                    <div class="relative pl-8 border-l-2 border-gray-100 space-y-8">
                        <div class="relative">
                            <div class="absolute -left-[41px] top-0 w-6 h-6 rounded-full bg-green-500 border-4 border-white shadow-sm flex items-center justify-center">
                                <CheckCircle2 class="w-3 h-3 text-white" />
                            </div>
                            <p class="text-sm font-bold text-gray-900">Đơn hàng đã được tạo thành công</p>
                            <p class="text-xs text-gray-400 mt-1">{{ new Date(order.created_at).toLocaleString('vi-VN') }}</p>
                        </div>
                        <div class="relative opacity-50">
                            <div class="absolute -left-[41px] top-0 w-6 h-6 rounded-full bg-gray-200 border-4 border-white"></div>
                            <p class="text-sm font-bold text-gray-900">Chuẩn bị hàng & Đóng gói</p>
                            <p class="text-xs text-gray-400 mt-1">Đang chờ xử lý...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Customer & Payment -->
            <div class="space-y-8">
                <!-- Customer Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <User class="w-5 h-5 text-gray-400" />
                        Khách mua hàng
                    </h2>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center font-bold text-gray-600 text-lg uppercase shadow-inner">
                                {{ order.customer_name?.charAt(0) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ order.customer_name }}</p>
                                <p class="text-xs text-gray-400">{{ order.customer_email }}</p>
                            </div>
                        </div>
                        <div class="pt-6 border-t border-gray-50 space-y-4">
                            <div class="flex gap-3">
                                <MapPin class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Địa chỉ giao hàng</p>
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ order.shipping_address }}</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <User class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Số điện thoại</p>
                                    <p class="text-sm text-gray-700">{{ order.customer_phone }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:scale-110 transition-transform">
                        <CreditCard class="w-20 h-20" />
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <CreditCard class="w-5 h-5 text-gray-400" />
                        Thanh toán
                    </h2>
                    <div class="space-y-6">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Phương thức</p>
                            <p class="text-sm font-bold text-gray-900">{{ order.payment_method }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Trạng thái</p>
                            <div class="flex items-center justify-between mt-2">
                                <span :class="paymentStatusMap[order.payment_status]?.color" class="px-3 py-1 rounded-lg text-xs font-bold">
                                    {{ paymentStatusMap[order.payment_status]?.label }}
                                </span>
                                <button 
                                    @click="updateStatus('payment_status', order.payment_status === 'paid' ? 'unpaid' : 'paid')"
                                    class="text-xs font-bold text-amber-600 hover:text-amber-700 underline underline-offset-4"
                                >
                                    Đổi sang {{ order.payment_status === 'paid' ? 'Chưa thanh toán' : 'Đã thanh toán' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
