<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Plus, Search, Edit2, Trash2, LibraryBig, Calendar } from 'lucide-vue-next';

const props = defineProps({ collections: Object, filters: Object });
const search = ref(props.filters?.search || '');

let debounce;
watch(search, (val) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(route('admin.collections.index'), { search: val || undefined }, { preserveState: true, replace: true });
    }, 300);
});

const deleteCollection = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa bộ sưu tập này không?')) router.delete(route('admin.collections.destroy', id));
};

const formatDate = (dateString) => {
    if (!dateString) return '—';
    return new Date(dateString).toLocaleDateString('vi-VN', { year: 'numeric', month: 'short', day: 'numeric' });
};
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Bộ sưu tập</h1>
                <p class="text-sm text-gray-500 mt-2 font-medium">Quản lý các bộ sưu tập sản phẩm (VD: Quà Tết, Rượu khói...). (Tổng cộng {{ collections.total }})</p>
            </div>
            <Link :href="route('admin.collections.create')" class="flex items-center gap-2 bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-amber-600 transition-colors shadow-sm w-fit">
                <Plus class="w-4 h-4" />
                Thêm bộ sưu tập
            </Link>
        </div>

        <div class="mb-6 flex items-center justify-between gap-4 bg-white p-2 border border-gray-100 rounded-2xl shadow-sm">
            <div class="relative w-full max-w-md">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Tìm theo tên..."
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none"
                />
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 w-[40%]">Chi tiết bộ sưu tập</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-center">Thời gian</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-center">Sản phẩm</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-if="collections.data.length === 0">
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-sm">Không tìm thấy bộ sưu tập nào.</td>
                    </tr>
                    <tr v-for="col in collections.data" :key="col.id" class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100">
                                    <LibraryBig class="w-5 h-5" />
                                </div>
                                <div class="flex flex-col">
                                    <div class="font-bold text-gray-900 text-[14px] line-clamp-1">{{ col.name }}</div>
                                    <div class="text-[11px] text-gray-400 mt-1 font-mono tracking-tighter">{{ col.slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span :class="col.is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-50 text-gray-500 border-gray-200'" class="px-3 py-1 text-[11px] rounded-full font-bold border tracking-wider uppercase">
                                {{ col.is_active ? 'Hoạt động' : 'Ẩn' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center text-xs text-gray-600 gap-1">
                                <span v-if="col.start_date || col.end_date" class="flex items-center gap-1 font-medium bg-gray-50 px-2 py-1 rounded border border-gray-200">
                                    <Calendar class="w-3 h-3 text-gray-400" />
                                    {{ formatDate(col.start_date) }} → {{ formatDate(col.end_date) }}
                                </span>
                                <span v-else class="text-gray-400 italic">Luôn hiển thị</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-600 font-bold px-2 py-1 rounded-md text-xs border border-gray-200">{{ col.products_count }} sản phẩm</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <Link :href="route('admin.collections.edit', col.id)" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Sửa">
                                    <Edit2 class="w-4 h-4" />
                                </Link>
                                <button @click="deleteCollection(col.id)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Xóa">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between" v-if="collections.last_page > 1">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Trang {{ collections.current_page }} / {{ collections.last_page }}
                </span>
                <div class="flex gap-1">
                    <Link
                        v-for="link in collections.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        :class="[link.active ? 'bg-gray-900 text-white border-transparent' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-200', !link.url ? 'opacity-50 cursor-not-allowed' : '']"
                        class="min-w-[32px] h-8 flex items-center justify-center px-2 text-xs font-bold border rounded-lg transition-colors"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
