<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import MediaPicker from '@/Components/Admin/MediaPicker.vue';
import RichEditor from '@/Components/Admin/RichEditor.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Plus, Trash2 } from 'lucide-vue-next';

const props = defineProps({
    product: { type: Object, default: null },
    brands: Array,
    countries: Array,
    categories: Array,
});

const isEdit = !!props.product;

const form = useForm({
    name: props.product?.name || '',
    product_type: props.product?.product_type || 'whisky',
    brand_id: props.product?.brand_id || '',
    country_id: props.product?.country_id || '',
    sale_mode: props.product?.sale_mode || 'online_checkout',
    is_published: props.product?.is_published ?? true,
    is_featured: props.product?.is_featured ?? false,
    short_desc: props.product?.short_desc || '',
    long_desc_html: props.product?.long_desc_html || '',
    price: props.product?.variants?.[0]?.price || '',
    volume_ml: props.product?.variants?.[0]?.volume_ml || '',
    abv_percent: props.product?.variants?.[0]?.abv_percent || '',
    sku: props.product?.variants?.[0]?.sku || '',
    meta_title: props.product?.meta_title || '',
    meta_description: props.product?.meta_description || '',
    taxon_ids: props.product?.taxons?.map(t => t.id) || [],
    media_ids: props.product?.media_ids || [],
    attributes: props.product?.attributes || [],
});

const actualCategories = computed(() => props.categories.filter(t => t.taxon_type === 'category'));
const filterTaxons = computed(() => {
    const list = props.categories.filter(t => t.taxon_type === 'filter');
    // Group by parent if possible, but for now just list them
    return list;
});

const addAttribute = () => {
    form.attributes.push({ attr_key: '', attr_value: '' });
};

const removeAttribute = (index) => {
    form.attributes.splice(index, 1);
};

const submit = () => {
    if (isEdit) {
        form.put(route('admin.products.update', props.product.id));
    } else {
        form.post(route('admin.products.store'));
    }
};
</script>

<template>
    <AdminLayout>
        <div class="mb-6">
            <Link :href="route('admin.products.index')" class="text-sm text-gray-500 hover:text-gray-900">← Quay lại danh sách</Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ isEdit ? 'Chỉnh sửa Sản phẩm' : 'Thêm Sản phẩm mới' }}</h1>
        </div>

        <form @submit.prevent="submit" class="max-w-4xl space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left column (Form) -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                        <h2 class="font-semibold text-gray-900 border-b pb-3">Thông tin cơ bản</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên Sản phẩm *</label>
                    <input v-model="form.name" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none" placeholder="Nhập tên sản phẩm..." />
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thương hiệu</label>
                        <select v-model="form.brand_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                            <option value="">-- Chọn thương hiệu --</option>
                            <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quốc gia</label>
                        <select v-model="form.country_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                            <option value="">-- Chọn quốc gia --</option>
                            <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loại sản phẩm</label>
                        <select v-model="form.product_type" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                            <option value="whisky">Whisky</option>
                            <option value="cognac">Cognac</option>
                            <option value="wine">Vang (Wine)</option>
                            <option value="rum">Rum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chế độ bán hàng</label>
                        <select v-model="form.sale_mode" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                            <option value="online_checkout">Thanh toán Online</option>
                            <option value="inquiry_only">Chỉ nhận báo giá</option>
                            <option value="showroom_only">Chỉ bán tại cửa hàng</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục sản phẩm</label>
                    <div class="grid grid-cols-2 gap-2 p-4 border border-gray-100 rounded-lg bg-gray-50/50">
                        <label v-for="cat in actualCategories" :key="cat.id" class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer hover:text-gray-900">
                            <input type="checkbox" :value="cat.id" v-model="form.taxon_ids" class="rounded border-gray-300 text-amber-500 focus:ring-amber-400" />
                            {{ cat.name }}
                        </label>
                    </div>
                </div>

                <div v-if="filterTaxons.length > 0">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bộ lọc mở rộng (Tags/Filters)</label>
                    <div class="grid grid-cols-2 gap-2 p-4 border border-gray-100 rounded-lg bg-amber-50/30 border-amber-100">
                        <label v-for="f in filterTaxons" :key="f.id" class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer hover:text-gray-900">
                            <input type="checkbox" :value="f.id" v-model="form.taxon_ids" class="rounded border-gray-300 text-amber-500 focus:ring-amber-400" />
                            {{ f.name }}
                        </label>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1 italic">Chọn các thuộc tính bổ sung để khách hàng dễ dàng tìm kiếm.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
                    <textarea v-model="form.short_desc" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="Tóm tắt về sản phẩm..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả chi tiết</label>
                    <RichEditor v-model="form.long_desc_html" placeholder="Viết mô tả chi tiết về sản phẩm, ghi chú hương vị..." min-height="250px" />
                </div>

                <div class="flex gap-6">
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                        <input v-model="form.is_published" type="checkbox" class="rounded border-gray-300 text-amber-500 focus:ring-amber-400" />
                        Đang hiển thị (Published)
                    </label>
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                        <input v-model="form.is_featured" type="checkbox" class="rounded border-gray-300 text-amber-500 focus:ring-amber-400" />
                        Sản phẩm nổi bật (Featured)
                    </label>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <h2 class="font-semibold text-gray-900 border-b pb-3 text-lg">Thông số & Giá bán</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá (VND)</label>
                                <input v-model="form.price" type="number" step="1000" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none font-mono" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mã SKU</label>
                                <input v-model="form.sku" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none uppercase font-mono" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dung tích (ml)</label>
                                <input v-model="form.volume_ml" type="number" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nồng độ cồn (% ABV)</label>
                                <input v-model="form.abv_percent" type="number" step="0.01" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                        <div class="flex items-center justify-between border-b pb-3">
                            <h2 class="font-semibold text-gray-900 text-lg">Thông số kỹ thuật (Specs)</h2>
                            <button type="button" @click="addAttribute" class="text-xs bg-amber-50 text-amber-600 px-3 py-1.5 rounded-lg border border-amber-100 font-bold hover:bg-amber-100 transition-colors flex items-center gap-1">
                                <Plus class="w-3 h-3" /> THÊM THÔNG SỐ
                            </button>
                        </div>
                        
                        <div v-if="form.attributes.length === 0" class="py-10 text-center border-2 border-dashed border-gray-100 rounded-xl">
                            <p class="text-sm text-gray-400">Chưa có thông số kỹ thuật nào.</p>
                        </div>
                        
                        <div v-else class="space-y-3">
                            <div v-for="(attr, index) in form.attributes" :key="index" class="flex items-center gap-3">
                                <input v-model="attr.attr_key" type="text" class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="Ví dụ: Vùng (Region)" />
                                <input v-model="attr.attr_value" type="text" class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="Ví dụ: Speyside" />
                                <button type="button" @click="removeAttribute(index)" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 italic mt-2">Phần này dùng để hiển thị các thông tin chi tiết như: Loại thùng, Độ khói, Năm chưng cất...</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                        <h2 class="font-semibold text-gray-900 border-b pb-3 text-lg">Cấu hình SEO</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title (SEO)</label>
                                <input v-model="form.meta_title" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="Tiêu đề hiển thị trên Google..." />
                                <p class="text-[10px] text-gray-400 mt-1">Đề xuất: 50-60 ký tự. Nếu trống sẽ sử dụng tên sản phẩm.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description (SEO)</label>
                                <textarea v-model="form.meta_description" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="Mô tả súc tích cho bộ máy tìm kiếm..."></textarea>
                                <p class="text-[10px] text-gray-400 mt-1">Đề xuất: 150-160 ký tự.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right column (Media) -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="font-semibold text-gray-900 border-b pb-3 mb-4 text-lg">Hình ảnh Sản phẩm</h2>
                        <MediaPicker v-model="form.media_ids" :multiple="true" :initial-assets="product?.mediaAssets || []" />
                        <p class="text-xs text-gray-400 mt-4 leading-relaxed font-light italic">Chọn một hoặc nhiều hình ảnh. Ảnh đầu tiên sẽ được dùng làm ảnh đại diện chính.</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-3 sticky top-[100px]">
                        <button type="submit" :disabled="form.processing" class="w-full bg-gray-900 text-white px-8 py-3.5 rounded-xl text-sm font-bold hover:bg-amber-600 transition-all shadow-lg active:scale-95 disabled:opacity-50">
                            {{ form.processing ? 'Đang lưu...' : (isEdit ? 'Cập nhật Sản phẩm' : 'Đăng Sản phẩm') }}
                        </button>
                        <Link :href="route('admin.products.index')" class="w-full text-center px-8 py-3.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-500 hover:bg-gray-50 transition-all hover:text-gray-900">
                            Hủy bỏ
                        </Link>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
