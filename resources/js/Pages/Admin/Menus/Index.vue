<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Search, Plus, Pencil, Trash2, Menu as MenuIcon, ToggleLeft, ToggleRight } from 'lucide-vue-next';

const props = defineProps({ menus: Object, filters: Object });
const search = ref(props.filters?.search || '');
let debounce;

watch(search, (val) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(route('admin.menus.index'), { search: val }, { preserveState: true, replace: true });
    }, 300);
});

const deleteMenu = (menu) => {
    if (confirm(`Delete menu "${menu.name}"?`)) {
        router.delete(route('admin.menus.destroy', menu.id));
    }
};
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Thanh điều hướng (Menus)</h1>
                <p class="text-sm text-gray-500 mt-2 font-medium">Quản lý menu điều hướng trang web và các mục con. (Tổng cộng {{ menus.total }})</p>
            </div>
            <Link :href="route('admin.menus.create')" class="flex items-center gap-2 bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-amber-600 transition-colors shadow-sm">
                <Plus class="w-4 h-4" /> Thêm Menu
            </Link>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <div class="relative">
                    <Search class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" />
                    <input v-model="search" type="text" placeholder="Tìm menu theo tên..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none" />
                </div>
            </div>

            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 text-left">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Tên Menu</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Mã (Code)</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Vị trí</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Mục con</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="menu in menus.data" :key="menu.id" class="border-b border-gray-50 hover:bg-amber-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-100 to-amber-50 flex items-center justify-center flex-shrink-0">
                                    <MenuIcon class="w-5 h-5 text-amber-600" />
                                </div>
                                <div>
                                    <span class="font-bold text-gray-900 text-sm">{{ menu.name }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-md font-mono">{{ menu.code }}</code>
                        </td>
                        <td class="px-6 py-4">
                            <span v-if="menu.location" class="text-sm text-gray-600 font-medium">{{ menu.location }}</span>
                            <span v-else class="text-xs text-gray-400 italic">—</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-bold text-gray-700 bg-gray-100 px-3 py-1 rounded-full">{{ menu.all_items_count }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span v-if="menu.is_active" class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-full">
                                <ToggleRight class="w-3.5 h-3.5" /> Hoạt động
                            </span>
                            <span v-else class="inline-flex items-center gap-1 bg-gray-100 text-gray-500 text-xs font-bold px-2.5 py-1 rounded-full">
                                <ToggleLeft class="w-3.5 h-3.5" /> Đã ẩn
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="route('admin.menus.edit', menu.id)" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                    <Pencil class="w-4 h-4" />
                                </Link>
                                <button @click="deleteMenu(menu)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="menus.data.length === 0">
                        <td colspan="6" class="text-center py-16 text-gray-400">
                            <MenuIcon class="w-12 h-12 mx-auto mb-3 opacity-30" />
                            <p class="text-sm font-medium">Không tìm thấy menu nào.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
