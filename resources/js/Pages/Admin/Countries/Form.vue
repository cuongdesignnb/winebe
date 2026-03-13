<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ country: { type: Object, default: null } });
const isEdit = !!props.country;

const form = useForm({
    name: props.country?.name || '',
    iso_code: props.country?.iso_code || '',
});

const submit = () => {
    if (isEdit) form.put(route('admin.countries.update', props.country.id));
    else form.post(route('admin.countries.store'));
};
</script>

<template>
    <AdminLayout>
        <div class="mb-6">
            <Link :href="route('admin.countries.index')" class="text-sm text-gray-500 hover:text-gray-900">← Quay lại danh sách</Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ isEdit ? 'Chỉnh sửa Quốc gia' : 'Thêm Quốc gia mới' }}</h1>
        </div>
        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên quốc gia *</label>
                    <input v-model="form.name" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="VD: Scotland, Pháp, Nhật Bản..." />
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã ISO (2 chữ cái)</label>
                    <input v-model="form.iso_code" type="text" maxlength="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none uppercase" placeholder="VD: GB, FR, JP" />
                    <div v-if="form.errors.iso_code" class="text-red-500 text-xs mt-1">{{ form.errors.iso_code }}</div>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="bg-gray-900 text-white px-8 py-3.5 rounded-xl text-sm font-bold hover:bg-amber-600 transition-colors disabled:opacity-50 shadow-md">
                    {{ form.processing ? 'Đang lưu...' : (isEdit ? 'Cập nhật' : 'Tạo mới') }}
                </button>
                <Link :href="route('admin.countries.index')" class="px-8 py-3.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">Hủy</Link>
            </div>
        </form>
    </AdminLayout>
</template>
