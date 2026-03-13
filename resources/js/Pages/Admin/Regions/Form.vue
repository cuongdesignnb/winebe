<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    region: { type: Object, default: null },
    countries: Array,
});
const isEdit = !!props.region;

const form = useForm({
    country_id: props.region?.country_id || '',
    name: props.region?.name || '',
    description: props.region?.description || '',
});

const submit = () => {
    if (isEdit) form.put(route('admin.regions.update', props.region.id));
    else form.post(route('admin.regions.store'));
};
</script>

<template>
    <AdminLayout>
        <div class="mb-6">
            <Link :href="route('admin.regions.index')" class="text-sm text-gray-500 hover:text-gray-900">← Quay lại danh sách</Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ isEdit ? 'Chỉnh sửa Vùng/Khu vực' : 'Thêm Vùng mới' }}</h1>
        </div>
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quốc gia *</label>
                    <select v-model="form.country_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:bg-white focus:ring-2 focus:ring-amber-400 outline-none transition-colors">
                        <option value="" disabled>Chọn quốc gia</option>
                        <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                    <div v-if="form.errors.country_id" class="text-red-500 text-xs mt-1">{{ form.errors.country_id }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên vùng/khu vực *</label>
                    <input v-model="form.name" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="VD: Speyside, Islay, Highland..." />
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả (Tùy chọn)</label>
                    <textarea v-model="form.description" rows="4" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="Viết vài dòng giới thiệu về vùng này..."></textarea>
                    <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="bg-gray-900 text-white px-8 py-3.5 rounded-xl text-sm font-bold hover:bg-amber-600 transition-colors disabled:opacity-50 shadow-md">
                    {{ form.processing ? 'Đang lưu...' : (isEdit ? 'Cập nhật' : 'Tạo mới') }}
                </button>
                <Link :href="route('admin.regions.index')" class="px-8 py-3.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">Hủy</Link>
            </div>
        </form>
    </AdminLayout>
</template>
