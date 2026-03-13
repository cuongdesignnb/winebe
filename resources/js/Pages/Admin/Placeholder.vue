<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Construction } from 'lucide-vue-next';

defineProps({
    title: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <AdminLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
            <p class="text-sm text-gray-500 mt-1">This module is part of the system but is currently under active development.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-16 text-center">
            <div class="w-20 h-20 mx-auto bg-amber-50 rounded-full flex items-center justify-center mb-6">
                <Construction class="w-10 h-10 text-amber-500" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900">Module Coming Soon</h3>
            <p class="mt-2 text-base text-gray-500 max-w-md mx-auto">
                The management interface for <strong>{{ title }}</strong> will be available in future releases. All database schemas and backend structures are already prepared.
            </p>
        </div>
    </AdminLayout>
</template>
