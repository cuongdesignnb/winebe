<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Wine, ShoppingCart, MessageSquare, CheckCircle2, TrendingUp, AlertCircle, Clock } from 'lucide-vue-next';

defineProps({
    stats: Object,
});

const recentActivities = [
    { title: 'Đơn hàng mới nhận', time: '5 phút trước', type: 'order', color: 'bg-blue-500' },
    { title: 'Đã cập nhật The Macallan 18', time: '1 giờ trước', type: 'product', color: 'bg-amber-500' },
    { title: 'Đã tạo Danh mục "Single Malt"', time: '3 giờ trước', type: 'category', color: 'bg-purple-500' },
    { title: 'Cảnh báo kho: Hennessy XO sắp hết', time: '5 giờ trước', type: 'alert', color: 'bg-red-500' },
];
</script>

<template>
    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Tổng quan quản trị</h1>
                <p class="text-sm text-gray-500 mt-2 font-medium">Chào mừng trở lại trang quản trị BMGS. Sau đây là tình hình hôm nay.</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="bg-gray-100 hover:bg-gray-200 text-sm font-semibold text-gray-700 px-4 py-2 rounded-lg transition">Xuất dữ liệu</button>
                <button class="bg-gray-900 hover:bg-gray-800 text-sm font-semibold text-white px-4 py-2 rounded-lg transition shadow-sm">Xem báo cáo</button>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Products -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <Wine class="w-24 h-24" />
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 mb-4 shadow-[inset_0_1px_1px_rgba(255,255,255,1)]">
                    <Wine class="w-6 h-6" />
                </div>
                <p class="text-[13px] font-bold text-gray-500 tracking-wide uppercase mb-1">Tổng sản phẩm</p>
                <p class="text-4xl font-black text-gray-900">{{ stats.products }}</p>
                <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-green-600 bg-green-50 w-fit px-2 py-1 rounded-md">
                    <TrendingUp class="w-3 h-3" />
                    +12% tháng này
                </div>
            </div>

            <!-- Published -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <CheckCircle2 class="w-24 h-24" />
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 mb-4 shadow-[inset_0_1px_1px_rgba(255,255,255,1)]">
                    <CheckCircle2 class="w-6 h-6" />
                </div>
                <p class="text-[13px] font-bold text-gray-500 tracking-wide uppercase mb-1">Đang hiển thị</p>
                <p class="text-4xl font-black text-gray-900">{{ stats.published }}</p>
                <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-gray-500 w-fit">
                    Active trên website
                </div>
            </div>

            <!-- Orders -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <ShoppingCart class="w-24 h-24" />
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 mb-4 shadow-[inset_0_1px_1px_rgba(255,255,255,1)]">
                    <ShoppingCart class="w-6 h-6" />
                </div>
                <p class="text-[13px] font-bold text-gray-500 tracking-wide uppercase mb-1">Đơn hàng mới</p>
                <p class="text-4xl font-black text-gray-900">{{ stats.orders }}</p>
                <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-blue-600 bg-blue-50 w-fit px-2 py-1 rounded-md">
                    Chờ xử lý
                </div>
            </div>

            <!-- Inquiries -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <MessageSquare class="w-24 h-24" />
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 mb-4 shadow-[inset_0_1px_1px_rgba(255,255,255,1)]">
                    <MessageSquare class="w-6 h-6" />
                </div>
                <p class="text-[13px] font-bold text-gray-500 tracking-wide uppercase mb-1">Yêu cầu tư vấn</p>
                <p class="text-4xl font-black text-gray-900">{{ stats.inquiries }}</p>
                <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-gray-500 w-fit">
                    Cần phản hồi
                </div>
            </div>
        </div>

        <!-- Lower Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Mock Chart -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Doanh thu dự kiến</h3>
                    <select class="text-sm border-gray-200 rounded-lg text-gray-600 bg-gray-50 py-1.5 px-3 outline-none focus:ring-2 focus:ring-amber-400">
                        <option>30 ngày qua</option>
                        <option>Năm nay</option>
                    </select>
                </div>
                <div class="h-64 flex items-end gap-2 border-b border-gray-100 pb-2 relative">
                    <div class="absolute left-0 top-0 h-full flex flex-col justify-between text-[10px] text-gray-400 pb-2">
                        <span>50tr</span>
                        <span>25tr</span>
                        <span>0</span>
                    </div>
                    <div class="w-full pl-10 h-full flex items-end justify-between gap-2 overflow-hidden">
                        <div class="w-full bg-amber-100 rounded-t-sm h-[20%] hover:bg-amber-300 transition-colors duration-300 group relative"></div>
                        <div class="w-full bg-amber-200 rounded-t-sm h-[35%] hover:bg-amber-300 transition-colors duration-300"></div>
                        <div class="w-full bg-amber-100 rounded-t-sm h-[25%] hover:bg-amber-300 transition-colors duration-300"></div>
                        <div class="w-full bg-amber-400 rounded-t-sm h-[50%] hover:bg-amber-500 transition-colors duration-300"></div>
                        <div class="w-full bg-amber-300 rounded-t-sm h-[40%] hover:bg-amber-400 transition-colors duration-300"></div>
                        <div class="w-full bg-gray-900 rounded-t-sm h-[80%] hover:bg-gray-800 transition-colors duration-300 shadow-[0_0_15px_rgba(0,0,0,0.1)]"></div>
                        <div class="w-full bg-amber-200 rounded-t-sm h-[60%] hover:bg-amber-300 transition-colors duration-300"></div>
                    </div>
                </div>
                <div class="pl-10 flex justify-between tracking-tighter text-[10px] text-gray-400 mt-2 uppercase font-semibold">
                    <span>Th2</span><span>Th3</span><span>Th4</span><span>Th5</span><span>Th6</span><span>Th7</span><span>CN</span>
                </div>
            </div>

            <!-- Activity Feed -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <Clock class="w-5 h-5 text-gray-400" />
                    Hoạt động gần đây
                </h3>
                <div class="relative max-h-64 overflow-y-auto pr-2">
                    <div class="absolute left-[11px] top-2 bottom-2 w-0.5 bg-gray-100"></div>
                    <ul class="space-y-6">
                        <li v-for="(act, i) in recentActivities" :key="i" class="relative pl-8">
                            <div class="absolute left-0 top-1 w-6 h-6 rounded-full border-4 border-white flex items-center justify-center shadow-sm" :class="act.color"></div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ act.title }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ act.time }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <button class="w-full mt-6 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold transition-colors">
                    Xem toàn bộ nhật ký
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
