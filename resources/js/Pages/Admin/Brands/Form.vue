<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import MediaPicker from '@/Components/Admin/MediaPicker.vue';
import RichEditor from '@/Components/Admin/RichEditor.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ brand: { type: Object, default: null } });
const isEdit = !!props.brand;

const form = useForm({
    name: props.brand?.name || '',
    short_description: props.brand?.short_description || '',
    story_html: props.brand?.story_html || '',
    meta_title: props.brand?.meta_title || '',
    meta_description: props.brand?.meta_description || '',
    status: props.brand?.status || 'active',
    media_id: props.brand?.media_id || null,
});

const submit = () => {
    if (isEdit) form.put(route('admin.brands.update', props.brand.id));
    else form.post(route('admin.brands.store'));
};
</script>

<template>
    <AdminLayout>
        <div class="mb-6">
            <Link :href="route('admin.brands.index')" class="text-sm text-gray-500 hover:text-gray-900">← Quay lại danh sách</Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ isEdit ? 'Chỉnh sửa Thương hiệu' : 'Thêm Thương hiệu mới' }}</h1>
        </div>
        <form @submit.prevent="submit" class="max-w-4xl space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên Thương hiệu *</label>
                            <input v-model="form.name" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="VD: Macallan, Johnnie Walker..." />
                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
                            <textarea v-model="form.short_description" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Câu chuyện thương hiệu</label>
                            <RichEditor v-model="form.story_html" placeholder="Viết về lịch sử, di sản của thương hiệu..." min-height="200px" />
                        </div>

                        <div class="border-t pt-5 mt-5">
                            <h3 class="font-bold text-gray-900 mb-4 text-lg">Cấu hình SEO</h3>
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

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                            <select v-model="form.status" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                                <option value="active">Đang hoạt động</option>
                                <option value="inactive">Đang ẩn</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="font-semibold text-gray-900 border-b pb-3 mb-4">Logo Thương hiệu</h2>
                        <MediaPicker v-model="form.media_id" :multiple="false" />
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-3">
                        <button type="submit" :disabled="form.processing" class="w-full bg-gray-900 text-white px-8 py-3.5 rounded-xl text-sm font-bold hover:bg-amber-600 transition-colors disabled:opacity-50">
                            {{ form.processing ? 'Đang lưu...' : (isEdit ? 'Cập nhật' : 'Tạo mới') }}
                        </button>
                        <Link :href="route('admin.brands.index')" class="w-full text-center px-8 py-3.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Hủy</Link>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
