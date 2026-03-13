<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import MediaPicker from '@/Components/Admin/MediaPicker.vue';
import RichEditor from '@/Components/Admin/RichEditor.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ category: { type: Object, default: null }, parents: Array });
const isEdit = !!props.category;

const form = useForm({
    name: props.category?.name || '',
    parent_id: props.category?.parent_id || '',
    description: props.category?.description || '',
    content: props.category?.content || '',
    meta_title: props.category?.meta_title || '',
    meta_description: props.category?.meta_description || '',
    is_active: props.category?.is_active ?? true,
    taxon_type: props.category?.taxon_type || 'category',
    media_id: props.category?.media_id || null,
});

const submit = () => {
    if (isEdit) form.put(route('admin.categories.update', props.category.id));
    else form.post(route('admin.categories.store'));
};
</script>

<template>
    <AdminLayout>
        <div class="mb-6">
            <Link :href="route('admin.categories.index')" class="text-sm text-gray-500 hover:text-gray-900">← Quay lại danh mục & bộ lọc</Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ isEdit ? 'Chỉnh sửa Phân loại' : 'Thêm Phân loại mới' }}</h1>
        </div>
        <form @submit.prevent="submit" class="max-w-4xl space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên Hiển thị *</label>
                                <input v-model="form.name" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="Nhập tên..." />
                                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Loại Phân loại</label>
                                <select v-model="form.taxon_type" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                                    <option value="category">Danh mục sản phẩm</option>
                                    <option value="filter">Nhóm Bộ lọc (Attributes)</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục cha</label>
                            <select v-model="form.parent_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                                <option value="">— Cấp gốc (không có cha) —</option>
                                <option v-for="p in parents" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
                            <textarea v-model="form.description" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung SEO (Dưới danh sách sp)</label>
                            <RichEditor v-model="form.content" placeholder="Viết mô tả danh mục chuẩn SEO..." min-height="180px" />
                        </div>

                        <div class="border-t pt-5 mt-5">
                            <h3 class="font-bold text-gray-900 mb-4">Cấu hình SEO</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title (SEO)</label>
                                    <input v-model="form.meta_title" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description (SEO)</label>
                                    <textarea v-model="form.meta_description" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none"></textarea>
                                </div>
                            </div>
                        </div>

                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-amber-500 focus:ring-amber-400" />
                            Kích hoạt / Hiển thị
                        </label>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="font-semibold text-gray-900 border-b pb-3 mb-4">Ảnh danh mục</h2>
                        <MediaPicker v-model="form.media_id" :multiple="false" />
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-3">
                        <button type="submit" :disabled="form.processing" class="w-full bg-gray-900 text-white px-8 py-3.5 rounded-xl text-sm font-bold hover:bg-amber-600 transition-colors disabled:opacity-50 shadow-md">
                            {{ form.processing ? 'Đang lưu...' : (isEdit ? 'Cập nhật Danh mục' : 'Tạo Danh mục') }}
                        </button>
                        <Link :href="route('admin.categories.index')" class="w-full text-center px-8 py-3.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Hủy</Link>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
