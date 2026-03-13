<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    LayoutDashboard,
    PackageSearch,
    Tags,
    Folders,
    LibraryBig,
    MapPin,
    Trophy, // For collections/distilleries
    Globe, // For countries
    FileText,
    Menu as MenuIcon,
    Image, // For media
    Settings,
    Users,
    ChevronDown,
    ChevronRight,
    LogOut,
    Eye,
    Wine,
    ShoppingCart,
    MessageSquare,
    Ticket,
    BarChart3
} from 'lucide-vue-next';

const sidebarOpen = ref(true);
const page = usePage();

const menuGroups = [
    {
        title: 'TỔNG QUAN',
        items: [
            { name: 'Bảng điều khiển', route: 'admin.dashboard', icon: LayoutDashboard }
        ]
    },
    {
        title: 'BÁN HÀNG & CRM',
        items: [
            { name: 'Đơn hàng', route: 'admin.orders.index', icon: ShoppingCart },
            { name: 'Yêu cầu tư vấn', route: 'admin.inquiries.index', icon: MessageSquare },
            { name: 'Voucher & KM', route: 'admin.promotions.index', icon: Ticket },
        ]
    },
    {
        title: 'DANH MỤC SẢN PHẨM',
        items: [
            { name: 'Sản phẩm', route: 'admin.products.index', icon: Wine },
            { name: 'Danh mục', route: 'admin.categories.index', icon: Folders },
            { name: 'Bộ sưu tập', route: 'admin.collections.index', icon: LibraryBig },
            { name: 'Thương hiệu', route: 'admin.brands.index', icon: Tags },
            { name: 'Nhà chưng cất', route: 'admin.distilleries.index', icon: Trophy },
            { name: 'Vùng miền', route: 'admin.regions.index', icon: MapPin },
            { name: 'Quốc gia', route: 'admin.countries.index', icon: Globe },
        ]
    },
    {
        title: 'MARKETING & NỘI DUNG',
        items: [
            { name: 'Trang tĩnh', route: 'admin.pages.index', icon: FileText },
            { name: 'Menu', route: 'admin.menus.index', icon: MenuIcon },
            { name: 'Thư viện Media', route: 'admin.media.index', icon: Image },
        ]
    },
    {
        title: 'HỆ THỐNG',
        items: [
            { name: 'Nhân viên & Thành viên', route: 'admin.users.index', icon: Users },
            { name: 'Cấu hình', route: 'admin.settings.index', icon: Settings }
        ]
    },
    {
        title: 'BÁO CÁO & THỐNG KÊ',
        items: [
            { name: 'Phân tích doanh thu', route: 'admin.analytics.index', icon: BarChart3 },
        ]
    }
];

const checkActive = (itemRouteName) => {
    // Basic match check (e.g. admin.products.index should light up if current is admin.products.edit)
    const base = itemRouteName.replace('.index', '');
    return route().current(itemRouteName) || route().current(base + '.*');
};
</script>

<template>
    <div class="min-h-screen flex bg-[#f8f9fa] font-sans selection:bg-amber-100 selection:text-amber-900">
        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'w-[280px]' : 'w-[80px]'"
            class="bg-white border-r border-gray-200 transition-all duration-300 flex flex-col shrink-0 overflow-y-auto shadow-sm z-10"
        >
            <!-- Logo Section -->
            <div class="h-[72px] flex items-center justify-center border-b border-gray-100 shrink-0 sticky top-0 bg-white z-10 p-4">
                <Link href="/admin" class="flex items-center gap-3 w-full" :class="sidebarOpen ? 'px-2' : 'justify-center'">
                    <div class="w-10 h-10 bg-gray-900 text-white flex items-center justify-center rounded-xl shrink-0 font-serif text-xl italic font-bold">B</div>
                    <div v-if="sidebarOpen" class="flex flex-col">
                        <span class="text-xs font-bold tracking-[0.2em] text-gray-900 uppercase">BMGS</span>
                        <span class="text-[10px] text-gray-400 tracking-wider">HỆ QUẢN TRỊ</span>
                    </div>
                </Link>
            </div>

            <!-- Nav Groups -->
            <nav class="flex-1 py-6 space-y-8 px-4">
                <div v-for="(group, gIndex) in menuGroups" :key="gIndex">
                    <h4 v-if="sidebarOpen" class="text-[11px] font-bold text-gray-400 tracking-widest uppercase mb-3 px-3">
                        {{ group.title }}
                    </h4>
                    <div class="space-y-1">
                        <Link
                            v-for="item in group.items"
                            :key="item.route"
                            :href="route(item.route)"
                            :class="[
                                checkActive(item.route)
                                    ? 'bg-amber-50 text-amber-900 font-semibold before:absolute before:inset-y-0 before:left-0 before:w-1 before:bg-amber-500 before:rounded-r'
                                    : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900 font-medium',
                                'relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] transition-all group overflow-hidden'
                            ]"
                            :title="!sidebarOpen ? item.name : ''"
                        >
                            <component
                                :is="item.icon"
                                class="w-[18px] h-[18px] shrink-0"
                                :class="checkActive(item.route) ? 'text-amber-600' : 'text-gray-400 group-hover:text-gray-600'"
                            />
                            <span v-if="sidebarOpen" class="whitespace-nowrap">{{ item.name }}</span>
                            
                            <!-- Chevron for potential submenus future -->
                            <!-- <ChevronRight v-if="sidebarOpen && item.sub" class="ml-auto w-4 h-4 text-gray-300" /> -->
                        </Link>
                    </div>
                </div>
            </nav>

            <!-- User Context -->
            <div class="p-4 border-t border-gray-100 mt-auto sticky bottom-0 bg-white">
                <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100" :class="sidebarOpen ? '' : 'justify-center px-0 bg-transparent border-0'">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-gray-700 to-gray-900 text-white flex items-center justify-center text-xs font-bold uppercase shrink-0 shadow-sm">
                        {{ page.props.auth?.user?.name?.charAt(0) || 'A' }}
                    </div>
                    <div v-if="sidebarOpen" class="flex-1 min-w-0">
                        <p class="text-[13px] font-bold text-gray-900 truncate">{{ page.props.auth?.user?.name }}</p>
                        <p class="text-[11px] text-gray-500 truncate">{{ page.props.auth?.user?.email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col w-0">
            <!-- Topbar (Glassmorphism effect) -->
            <header class="h-[72px] bg-white/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-8 shrink-0 sticky top-0 z-20 shadow-sm">
                <!-- Left side -->
                <div class="flex items-center gap-4">
                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                    >
                        <MenuIcon class="w-5 h-5" />
                    </button>
                    <!-- Breadcrumbs/Page Title could go here dynamically if desired -->
                </div>

                <!-- Right side -->
                <div class="flex items-center gap-6">
                    <!-- Quick action View Storefront -->
                    <a href="/" target="_blank" class="flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-gray-900 transition-colors bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-full">
                        <Eye class="w-[14px] h-[14px]" />
                        Xem Cửa hàng
                    </a>

                    <div class="w-[1px] h-6 bg-gray-200"></div>

                    <!-- Logout -->
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-red-600 transition-colors"
                    >
                        <LogOut class="w-4 h-4" />
                        Đăng xuất
                    </Link>
                </div>
            </header>

            <!-- Page Content (Scrollable region) -->
            <main class="flex-1 p-8 overflow-y-auto">
                <div class="max-w-7xl mx-auto">
                    <!-- Flash Messages Placeholder -->
                    <div v-if="page.props.flash?.success" class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-sm flex items-center gap-3 text-sm">
                        <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center text-green-600 shrink-0">✓</div>
                        {{ page.props.flash.success }}
                    </div>
                    
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style>
/* Custom scrollbar for admin panel */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}
</style>
