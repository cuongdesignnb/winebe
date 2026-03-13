<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { MessageSquare, Phone, Mail, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    inquiries: Object
});

const updateStatus = (id, status) => {
    router.put(route('admin.inquiries.update', id), { status });
};

const deleteInquiry = (id) => {
    if (confirm('Xóa yêu cầu này?')) {
        router.delete(route('admin.inquiries.destroy', id));
    }
};
</script>

<template>
    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Yêu cầu Tư vấn</h1>
            <p class="text-sm text-gray-500 mt-2">Danh sách khách hàng quan tâm đến các sản phẩm Inquiry-only.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Khách hàng</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Sản phẩm</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Lời nhắn</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="item in inquiries.data" :key="item.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm">
                            <div class="font-bold text-gray-900">{{ item.name }}</div>
                            <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
                                <Phone size="12" /> {{ item.phone }}
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <Mail size="12" /> {{ item.email }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                            {{ item.product?.name || 'Sản phẩm đã bị xóa' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                            {{ item.message }}
                        </td>
                        <td class="px-6 py-4">
                            <select @change="(e) => updateStatus(item.id, e.target.value)" :value="item.status" class="text-xs font-bold px-3 py-1.5 rounded-lg border-gray-200 focus:ring-amber-400">
                                <option value="new">Mới</option>
                                <option value="contacted">Đã liên hệ</option>
                                <option value="closed">Đã xong</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button @click="deleteInquiry(item.id)" class="text-red-400 hover:text-red-600 transition-colors p-2">
                                <Trash2 size="18" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
