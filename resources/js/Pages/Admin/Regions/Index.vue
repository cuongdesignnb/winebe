<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Plus, Search, Edit2, Trash2, MapPin } from 'lucide-vue-next';

const props = defineProps({ regions: Object, filters: Object });
const search = ref(props.filters?.search || '');

let debounce;
watch(search, (val) => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(route('admin.regions.index'), { search: val || undefined }, { preserveState: true, replace: true });
    }, 300);
});

const deleteRegion = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa vùng miền này vĩnh viễn không?')) router.delete(route('admin.regions.destroy', id));
};
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Vùng miền & Phân hạng</h1>
                <p class="text-sm text-gray-500 mt-2 font-medium">Quản lý các vùng miền như Speyside, Islay, Bordeaux... (Tổng cộng {{ regions.total }})</p>
            </div>
            <Link :href="route('admin.regions.create')" class="flex items-center gap-2 bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-amber-600 transition-colors shadow-sm w-fit">
                <Plus class="w-4 h-4" />
                Thêm vùng miền
            </Link>
        </div>

        <div class="mb-6 flex items-center justify-between gap-4 bg-white p-2 border border-gray-100 rounded-2xl shadow-sm">
            <div class="relative w-full max-w-md">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Tìm vùng miền theo tên..."
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none"
                />
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 w-[50%]">Thông tin vùng miền</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-center">Quốc gia</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-center">Số nhà chưng cất</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-if="regions.data.length === 0">
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm">Không tìm thấy vùng miền phù hợp với tìm kiếm.</td>
                    </tr>
                    <tr v-for="region in regions.data" :key="region.id" class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center shrink-0 border border-orange-100">
                                    <MapPin class="w-5 h-5" />
                                </div>
                                <div class="flex flex-col">
                                    <div class="font-bold text-gray-900 text-[14px] line-clamp-1">{{ region.name }}</div>
                                    <div class="text-[11px] text-gray-400 mt-1 font-mono tracking-tighter">{{ region.slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-600 font-bold px-3 py-1.5 rounded-lg text-xs border border-gray-200 uppercase">{{ region.country?.name || '—' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-gray-100 text-gray-600 font-bold px-2 py-1 rounded-md text-xs border border-gray-200">{{ region.distilleries_count || 0 }} nhà chưng cất</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <Link :href="route('admin.regions.edit', region.id)" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Sửa">
                                    <Edit2 class="w-4 h-4" />
                                </Link>
                                <button @click="deleteRegion(region.id)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Xóa">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between" v-if="regions.last_page > 1">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Đang xem trang {{ regions.current_page }} / {{ regions.last_page }}
                </span>
                <div class="flex gap-1">
                    <Link
                        v-for="link in regions.links"
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
