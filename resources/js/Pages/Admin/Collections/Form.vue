<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    collection: { type: Object, default: null },
});
const isEdit = !!props.collection;

const formatDateTimeLocally = (dtStr) => {
    if (!dtStr) return '';
    const dt = new Date(dtStr);
    return new Date(dt.getTime() - dt.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
};

const form = useForm({
    name: props.collection?.name || '',
    description: props.collection?.description || '',
    meta_title: props.collection?.meta_title || '',
    meta_description: props.collection?.meta_description || '',
    is_active: props.collection?.is_active ?? true,
    start_date: formatDateTimeLocally(props.collection?.start_date),
    end_date: formatDateTimeLocally(props.collection?.end_date),
});

const submit = () => {
    if (isEdit) form.put(route('admin.collections.update', props.collection.id));
    else form.post(route('admin.collections.store'));
};
</script>

<template>
    <AdminLayout>
        <div class="mb-6">
            <Link :href="route('admin.collections.index')" class="text-sm text-gray-500 hover:text-gray-900">← Quay lại danh sách</Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ isEdit ? 'Chỉnh sửa Bộ sưu tập' : 'Thêm Bộ sưu tập mới' }}</h1>
        </div>
        <form @submit.prevent="submit" class="max-w-3xl space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên bộ sưu tập *</label>
                    <input v-model="form.name" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="VD: Quà Tết, Rượu khói độc bản..." />
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea v-model="form.description" rows="4" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="Chi tiết về bộ sưu tập này..."></textarea>
                    <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
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

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu (Tùy chọn)</label>
                        <input v-model="form.start_date" type="datetime-local" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:bg-white focus:ring-2 focus:ring-amber-400 outline-none transition-colors" />
                        <div v-if="form.errors.start_date" class="text-red-500 text-xs mt-1">{{ form.errors.start_date }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc (Tùy chọn)</label>
                        <input v-model="form.end_date" type="datetime-local" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:bg-white focus:ring-2 focus:ring-amber-400 outline-none transition-colors" />
                        <div v-if="form.errors.end_date" class="text-red-500 text-xs mt-1">{{ form.errors.end_date }}</div>
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input v-model="form.is_active" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400" />
                    <span class="font-medium text-gray-700">Kích hoạt (Hiển thị trên cửa hàng)</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="bg-gray-900 text-white px-8 py-3.5 rounded-xl text-sm font-bold hover:bg-amber-600 transition-colors disabled:opacity-50 shadow-md">
                    {{ form.processing ? 'Đang lưu...' : (isEdit ? 'Cập nhật Bộ sưu tập' : 'Tạo Bộ sưu tập') }}
                </button>
                <Link :href="route('admin.collections.index')" class="px-8 py-3.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">Hủy</Link>
            </div>
        </form>
    </AdminLayout>
</template>
