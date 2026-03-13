<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import MediaPicker from '@/Components/Admin/MediaPicker.vue';
import RichEditor from '@/Components/Admin/RichEditor.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Save, Eye, FileText } from 'lucide-vue-next';

const props = defineProps({
    page: { type: Object, default: null },
});

const isEdit = !!props.page;

const form = useForm({
    title: props.page?.title || '',
    slug: props.page?.slug || '',
    page_type: props.page?.page_type || 'static',
    status: props.page?.status || 'draft',
    excerpt: props.page?.excerpt || '',
    body_html: props.page?.body_html || '',
    meta_title: props.page?.meta_title || '',
    meta_description: props.page?.meta_description || '',
    published_at: props.page?.published_at ? props.page.published_at.substring(0, 16) : '',
    media_id: props.page?.media_id || null,
});

// Auto-generate slug from title
const autoSlug = ref(!isEdit);
watch(() => form.title, (val) => {
    if (autoSlug.value && val) {
        form.slug = val.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Remove accents
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
    }
});

const submit = () => {
    if (isEdit) form.put(route('admin.pages.update', props.page.id));
    else form.post(route('admin.pages.store'));
};

const charCount = computed(() => {
    const text = form.body_html.replace(/<[^>]*>/g, '');
    return text.length;
});

const wordCount = computed(() => {
    const text = form.body_html.replace(/<[^>]*>/g, '').trim();
    return text ? text.split(/\s+/).length : 0;
});

const previewUrl = computed(() => {
    if (isEdit && props.page?.slug) return '/' + props.page.slug;
    return null;
});
</script>

<template>
    <AdminLayout>
        <div class="mb-6">
            <Link :href="route('admin.pages.index')" class="text-sm text-gray-500 hover:text-gray-900">← Quay lại danh sách</Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ isEdit ? 'Chỉnh sửa Trang' : 'Tạo Trang mới' }}</h1>
        </div>

        <form @submit.prevent="submit" class="max-w-5xl space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content (Left 2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title & Slug -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề Trang *</label>
                            <input v-model="form.title" type="text" class="w-full px-4 py-3 border border-gray-200 rounded-lg text-base font-semibold focus:ring-2 focus:ring-amber-400 outline-none" placeholder="Nhập tiêu đề trang..." />
                            <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <label class="text-sm font-medium text-gray-700">Đường dẫn (Slug)</label>
                                <label v-if="!isEdit" class="flex items-center gap-1 text-xs text-gray-400 cursor-pointer">
                                    <input v-model="autoSlug" type="checkbox" class="rounded border-gray-300 text-amber-500 w-3.5 h-3.5" />
                                    Tự động
                                </label>
                            </div>
                            <div class="flex items-center gap-0 border border-gray-200 rounded-lg overflow-hidden">
                                <span class="bg-gray-50 px-3 py-2.5 text-sm text-gray-400 border-r border-gray-200 flex-shrink-0 font-mono">/</span>
                                <input v-model="form.slug" type="text" class="flex-1 px-3 py-2.5 text-sm font-mono focus:ring-2 focus:ring-amber-400 outline-none" placeholder="duong-dan-trang" />
                            </div>
                            <div v-if="form.errors.slug" class="text-red-500 text-xs mt-1">{{ form.errors.slug }}</div>
                        </div>
                    </div>

                    <!-- Excerpt -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn (Trích dẫn)</label>
                        <textarea v-model="form.excerpt" rows="2" maxlength="500" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="Tóm tắt ngắn gọn nội dung trang..."></textarea>
                        <div class="text-xs text-gray-400 text-right mt-1">{{ (form.excerpt || '').length }}/500</div>
                    </div>

                    <!-- Body Content -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-gray-700 font-bold">Nội dung trang</label>
                            <div class="flex items-center gap-3 text-xs text-gray-400">
                                <span>{{ wordCount }} từ</span>
                                <span>{{ charCount }} ký tự</span>
                            </div>
                        </div>
                        <RichEditor v-model="form.body_html" placeholder="Bắt đầu viết nội dung trang..." min-height="350px" />
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-4 text-lg">Cấu hình SEO</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title (SEO)</label>
                                <input v-model="form.meta_title" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="Tiêu đề hiển thị trên Google..." />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description (SEO)</label>
                                <textarea v-model="form.meta_description" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="Mô tả hiển thị trên kết quả tìm kiếm..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar (Right 1/3) -->
                <div class="space-y-6">
                    <!-- Publish Settings -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                        <h3 class="font-semibold text-gray-900 border-b pb-3">Cấu hình xuất bản</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                            <select v-model="form.status" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none font-medium">
                                <option value="draft">📝 Bản nháp</option>
                                <option value="published">✅ Xuất bản</option>
                                <option value="archived">📦 Lưu trữ</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loại trang</label>
                            <select v-model="form.page_type" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none font-medium">
                                <option value="static">📄 Trang tĩnh</option>
                                <option value="blog">📝 Bài viết Blog</option>
                                <option value="landing">🚀 Landing Page</option>
                                <option value="faq">❓ Trang FAQ</option>
                            </select>
                        </div>
                        <div v-if="form.status === 'published'">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày xuất bản</label>
                            <input v-model="form.published_at" type="datetime-local" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" />
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-900 border-b pb-3 mb-4">Ảnh đại diện</h3>
                        <MediaPicker v-model="form.media_id" :multiple="false" />
                    </div>

                    <!-- Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-3">
                        <button type="submit" :disabled="form.processing" class="w-full bg-gray-900 text-white px-6 py-3.5 rounded-xl text-sm font-bold hover:bg-amber-600 transition-colors disabled:opacity-50 flex items-center justify-center gap-2 shadow-md">
                            <Save class="w-4 h-4" />
                            {{ form.processing ? 'Đang lưu...' : (isEdit ? 'Cập nhật Trang' : 'Tạo Trang') }}
                        </button>
                        <a v-if="previewUrl" :href="previewUrl" target="_blank" class="w-full flex items-center justify-center gap-2 px-6 py-3.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                            <Eye class="w-4 h-4" /> Xem thử
                        </a>
                        <Link :href="route('admin.pages.index')" class="w-full text-center px-6 py-3.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                            Hủy
                        </Link>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
