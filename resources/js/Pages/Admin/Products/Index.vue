<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Plus, Search, Edit2, Trash2, Eye, Wine } from 'lucide-vue-next';

const props = defineProps({
    products: Object,
    filters: Object,
    brands: Array,
    countries: Array,
    categories: Array,
});

const search = ref(props.filters?.search || '');
const brandFilter = ref(props.filters?.brand_id || '');
const countryFilter = ref(props.filters?.country_id || '');
const taxonFilter = ref(props.filters?.taxon_id || '');
const statusFilter = ref(props.filters?.is_published ?? '');

let debounce;
const applyFilters = () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(route('admin.products.index'), {
            search: search.value || undefined,
            brand_id: brandFilter.value || undefined,
            country_id: countryFilter.value || undefined,
            taxon_id: taxonFilter.value || undefined,
            is_published: statusFilter.value !== '' ? statusFilter.value : undefined,
        }, { preserveState: true, replace: true });
    }, 300);
};

watch([search, brandFilter, countryFilter, taxonFilter, statusFilter], applyFilters);

const deleteProduct = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')) {
        router.delete(route('admin.products.destroy', id));
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price || 0);
};
</script>

<template>
    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Sản phẩm</h1>
                <p class="text-sm text-gray-500 mt-2 font-medium">Quản lý danh mục sản phẩm, giá bán và thuộc tính tại đây. (Tổng cộng {{ products.total }} mục)</p>
            </div>
            <Link :href="route('admin.products.create')" class="flex items-center gap-2 bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-amber-600 transition-colors shadow-sm w-fit">
                <Plus class="w-4 h-4" />
                Thêm sản phẩm
            </Link>
        </div>

        <!-- Filters area -->
        <div class="mb-6 bg-white p-4 border border-gray-100 rounded-2xl shadow-sm space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="relative lg:col-span-2">
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Tìm theo tên hoặc mã SKU..."
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none"
                    />
                </div>
                
                <select v-model="brandFilter" class="w-full px-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none font-medium">
                    <option value="">Tất cả thương hiệu</option>
                    <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                </select>

                <select v-model="countryFilter" class="w-full px-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none font-medium">
                    <option value="">Tất cả quốc gia</option>
                    <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>

                <select v-model="statusFilter" class="w-full px-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none font-medium">
                    <option value="">Trạng thái hiển thị</option>
                    <option value="1">Đang hiển thị</option>
                    <option value="0">Đang ẩn</option>
                </select>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex-1 flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
                    <button 
                        @click="taxonFilter = ''" 
                        :class="taxonFilter === '' ? 'bg-amber-100 text-amber-700 font-bold border-amber-200' : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border-gray-100'"
                        class="px-4 py-1.5 text-xs rounded-full border transition-colors whitespace-nowrap"
                    >
                        Tất cả danh mục
                    </button>
                    <button 
                        v-for="cat in categories" 
                        :key="cat.id"
                        @click="taxonFilter = cat.id"
                        :class="taxonFilter == cat.id ? 'bg-amber-100 text-amber-700 font-bold border-amber-200' : 'bg-gray-50 text-gray-600 hover:bg-gray-100 border-gray-100'"
                        class="px-4 py-1.5 text-xs rounded-full border transition-colors whitespace-nowrap"
                    >
                        {{ cat.name }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Sản phẩm</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Thương hiệu / Xuất xứ</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Loại</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-right">Giá</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-if="products.data.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</td>
                    </tr>
                    <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center shrink-0">
                                    <Wine class="w-5 h-5 text-gray-300" />
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 text-[14px] line-clamp-1">{{ product.name }}</div>
                                    <div class="text-[11px] text-gray-400 mt-1 font-mono tracking-tighter uppercase">{{ product.variants?.[0]?.sku || 'No SKU' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-700">{{ product.brand?.name || '—' }}</span>
                                <span class="text-[11px] text-gray-400 font-medium">{{ product.country?.name || '—' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span v-if="product.product_type" class="px-2.5 py-1 bg-gray-100 text-gray-600 font-bold text-[10px] rounded-md uppercase tracking-wider border border-gray-200">
                                {{ product.product_type }}
                            </span>
                            <span v-else class="text-gray-400 text-sm">—</span>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                            {{ product.variants?.[0]?.price ? formatPrice(product.variants[0].price) : 'Liên hệ' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span :class="product.is_published ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'" class="px-3 py-1 text-[11px] rounded-full font-bold border tracking-wider uppercase">
                                {{ product.is_published ? 'Hiển thị' : 'Đang ẩn' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a :href="`/san-pham/${product.slug}`" target="_blank" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem trên Web">
                                    <Eye class="w-4 h-4" />
                                </a>
                                <Link :href="route('admin.products.edit', product.id)" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Sửa">
                                    <Edit2 class="w-4 h-4" />
                                </Link>
                                <button @click="deleteProduct(product.id)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Xóa">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between" v-if="products.last_page > 1">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Trang {{ products.current_page }} / {{ products.last_page }}
                </span>
                <div class="flex gap-1">
                    <Link
                        v-for="link in products.links"
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
