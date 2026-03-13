<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Search, Plus, Pencil, Trash2, FileText, Eye, Clock, Archive, Filter } from 'lucide-vue-next';

const props = defineProps({ pages: Object, filters: Object });
const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');
const typeFilter = ref(props.filters?.type || '');
let debounce;

const applyFilters = () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(route('admin.pages.index'), {
            search: search.value || undefined,
            status: statusFilter.value || undefined,
            type: typeFilter.value || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
};

watch(search, applyFilters);
watch(statusFilter, applyFilters);
watch(typeFilter, applyFilters);

const deletePage = (page) => {
    if (confirm(`Bạn có chắc chắn muốn xóa trang "${page.title}" không?`)) {
        router.delete(route('admin.pages.destroy', page.id));
    }
};

const statusConfig = {
    draft: { label: 'Bản nháp', class: 'bg-yellow-50 text-yellow-700', icon: Clock },
    published: { label: 'Đã xuất bản', class: 'bg-emerald-50 text-emerald-700', icon: Eye },
    archived: { label: 'Lưu trữ', class: 'bg-gray-100 text-gray-500', icon: Archive },
};

const typeLabels = {
    static: 'Trang tĩnh',
    blog: 'Bài viết Blog',
    landing: 'Trang Landing',
    faq: 'Trang FAQ',
};

const formatDate = (d) => {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
};
</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Trang nội dung (CMS)</h1>
                <p class="text-sm text-gray-500 mt-2 font-medium">Quản lý các trang tĩnh, bài viết blog và trang đích. (Tổng cộng {{ pages.total }})</p>
            </div>
            <Link :href="route('admin.pages.create')" class="flex items-center gap-2 bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-amber-600 transition-colors shadow-sm">
                <Plus class="w-4 h-4" /> Tạo trang mới
            </Link>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Filters -->
            <div class="p-4 border-b border-gray-100 flex flex-col md:flex-row gap-3">
                <div class="relative flex-1">
                    <Search class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" />
                    <input v-model="search" type="text" placeholder="Tìm kiếm trang..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none" />
                </div>
                <select v-model="statusFilter" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none min-w-[140px] font-medium">
                    <option value="">Tất cả trạng thái</option>
                    <option value="draft">Bản nháp</option>
                    <option value="published">Đã xuất bản</option>
                    <option value="archived">Lưu trữ</option>
                </select>
                <select v-model="typeFilter" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none min-w-[150px] font-medium">
                    <option value="">Tất cả loại trang</option>
                    <option value="static">Trang tĩnh</option>
                    <option value="blog">Bài viết Blog</option>
                    <option value="landing">Trang Landing</option>
                    <option value="faq">Trang FAQ</option>
                </select>
            </div>

            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 text-left bg-gray-50/50">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Trang</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Loại</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Đường dẫn</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Ngày đăng</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="page in pages.data" :key="page.id" class="border-b border-gray-50 hover:bg-amber-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center flex-shrink-0 border border-indigo-100">
                                    <FileText class="w-5 h-5 text-indigo-600" />
                                </div>
                                <div class="min-w-0">
                                    <span class="font-bold text-gray-900 text-sm block truncate max-w-[250px]">{{ page.title }}</span>
                                    <span v-if="page.excerpt" class="text-xs text-gray-400 block truncate max-w-[250px]">{{ page.excerpt }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-semibold bg-gray-100 text-gray-600 px-2.5 py-1 rounded-lg border border-gray-200">{{ typeLabels[page.page_type] || page.page_type }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-xs bg-gray-50 text-gray-500 px-2 py-1 rounded-md font-mono border border-gray-100">/{{ page.slug }}</code>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span :class="[statusConfig[page.status]?.class || 'bg-gray-100 text-gray-500']" class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border">
                                <component :is="statusConfig[page.status]?.icon" class="w-3.5 h-3.5" />
                                {{ statusConfig[page.status]?.label || page.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ formatDate(page.published_at) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="route('admin.pages.edit', page.id)" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Sửa">
                                    <Pencil class="w-4 h-4" />
                                </Link>
                                <button @click="deletePage(page)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Xóa">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="pages.data.length === 0">
                        <td colspan="6" class="text-center py-16 text-gray-400">
                            <FileText class="w-12 h-12 mx-auto mb-3 opacity-30" />
                            <p class="text-sm font-medium">Không tìm thấy trang nào.</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="pages.last_page > 1" class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/30">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Trang {{ pages.current_page }} / {{ pages.last_page }}</span>
                <div class="flex gap-1">
                    <template v-for="link in pages.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url" class="min-w-[32px] h-8 flex items-center justify-center px-2 text-xs font-bold border rounded-lg transition-colors" :class="link.active ? 'bg-gray-900 text-white border-transparent' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-200'" v-html="link.label" />
                        <span v-else class="min-w-[32px] h-8 flex items-center justify-center px-2 text-xs font-bold text-gray-300 border border-gray-100 rounded-lg" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
