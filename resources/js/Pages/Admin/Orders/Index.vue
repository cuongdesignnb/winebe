<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ShoppingBag, Eye, Clock, CheckCircle, XCircle } from 'lucide-react';

const props = defineProps({
    orders: Object
});

const getStatusClass = (status) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-700';
        case 'completed': return 'bg-green-100 text-green-700';
        case 'cancelled': return 'bg-red-100 text-red-700';
        default: return 'bg-blue-100 text-blue-700';
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Quản lý Đơn hàng</h1>
            <p class="text-sm text-gray-500 mt-2">Theo dõi và xử lý các đơn đặt hàng từ khách hàng.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Mã đơn</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Khách hàng</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tổng tiền</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Ngày đặt</th>
                        <th class="px-6 py-4 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-sm font-bold text-gray-900">#{{ order.order_number }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900">{{ order.customer_name }}</div>
                            <div class="text-xs text-gray-400">{{ order.customer_phone }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-amber-600">{{ formatPrice(order.total_amount) }}</td>
                        <td class="px-6 py-4">
                            <span :class="['px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider', getStatusClass(order.status)]">
                                {{ order.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ new Date(order.created_at).toLocaleDateString('vi-VN') }}</td>
                        <td class="px-6 py-4 text-right">
                            <Link :href="route('admin.orders.show', order.id)" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-900 bg-gray-100 px-3 py-1.5 rounded-lg hover:bg-gray-200 transition-colors">
                                <Eye class="w-3.5 h-3.5" /> Chi tiết
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="orders.data.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">Chưa có đơn hàng nào.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
