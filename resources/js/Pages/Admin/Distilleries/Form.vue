<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    distillery: { type: Object, default: null },
    regions: Array,
});
const isEdit = !!props.distillery;

const form = useForm({
    region_id: props.distillery?.region_id || '',
    name: props.distillery?.name || '',
    description: props.distillery?.description || '',
});

const submit = () => {
    if (isEdit) form.put(route('admin.distilleries.update', props.distillery.id));
    else form.post(route('admin.distilleries.store'));
};
</script>

<template>
    <AdminLayout>
        <div class="mb-6">
            <Link :href="route('admin.distilleries.index')" class="text-sm text-gray-500 hover:text-gray-900">← Quay lại danh sách</Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ isEdit ? 'Chỉnh sửa Nhà chưng cất' : 'Thêm Nhà chưng cất mới' }}</h1>
        </div>
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên nhà chưng cất *</label>
                    <input v-model="form.name" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="VD: Macallan, Glenfiddich..." />
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vùng/Khu vực (Tùy chọn)</label>
                    <select v-model="form.region_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:bg-white focus:ring-2 focus:ring-amber-400 outline-none transition-colors">
                        <option value="">— Không chọn —</option>
                        <option v-for="r in regions" :key="r.id" :value="r.id">{{ r.name }}</option>
                    </select>
                    <div v-if="form.errors.region_id" class="text-red-500 text-xs mt-1">{{ form.errors.region_id }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea v-model="form.description" rows="4" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="Thông tin chi tiết về nhà chưng cất này..."></textarea>
                    <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="bg-gray-900 text-white px-8 py-3.5 rounded-xl text-sm font-bold hover:bg-amber-600 transition-colors disabled:opacity-50 shadow-md">
                    {{ form.processing ? 'Đang lưu...' : (isEdit ? 'Cập nhật' : 'Tạo mới') }}
                </button>
                <Link :href="route('admin.distilleries.index')" class="px-8 py-3.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">Hủy</Link>
            </div>
        </form>
    </AdminLayout>
</template>
