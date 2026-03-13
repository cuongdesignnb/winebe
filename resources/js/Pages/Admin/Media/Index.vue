<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Upload, FolderPlus, Folder, Image as ImageIcon, Trash2, X, ChevronRight, Home } from 'lucide-vue-next';

const currentFolderId = ref(null);
const folders = ref([]);
const assets = ref([]);
const breadcrumbs = ref([]);
const loading = ref(false);

const isDragging = ref(false);
const showCreateFolderModal = ref(false);
const newFolderName = ref('');
const fileInput = ref(null);
const uploading = ref(false);

const fetchMedia = async (folderId = null) => {
    loading.value = true;
    try {
        const response = await axios.get(route('admin.media.api.folders.index', { parent_id: folderId }));
        folders.value = response.data.folders;
        assets.value = response.data.assets;
        breadcrumbs.value = response.data.path;
        currentFolderId.value = folderId;
    } catch (error) {
        console.error('Error fetching media:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchMedia();
});

const handleNavigate = (folderId) => {
    fetchMedia(folderId);
};

const createFolder = async () => {
    if (!newFolderName.value) return;
    try {
        await axios.post(route('admin.media.api.folders.store'), {
            name: newFolderName.value,
            parent_id: currentFolderId.value
        });
        showCreateFolderModal.value = false;
        newFolderName.value = '';
        fetchMedia(currentFolderId.value);
    } catch (error) {
        console.error('Error creating folder:', error);
    }
};

const deleteFolder = async (folder) => {
    if (!confirm('Are you sure you want to delete this folder?')) return;
    try {
        await axios.delete(route('admin.media.api.folders.destroy', folder.id));
        fetchMedia(currentFolderId.value);
    } catch (error) {
        alert(error.response?.data?.error || 'Failed to delete folder.');
    }
};

const deleteAsset = async (asset) => {
    if (!confirm('Are you sure you want to delete this image?')) return;
    try {
        await axios.delete(route('admin.media.api.assets.destroy', asset.id));
        fetchMedia(currentFolderId.value);
    } catch (error) {
        console.error('Failed to delete asset:', error);
    }
};

const triggerFileUpload = () => {
    fileInput.value.click();
};

const handleFileUpload = async (event) => {
    const files = event.target.files;
    if (!files.length) return;
    await uploadFiles(files);
};

const handleDrop = async (event) => {
    isDragging.value = false;
    const files = event.dataTransfer.files;
    if (!files.length) return;
    await uploadFiles(files);
};

const uploadFiles = async (files) => {
    uploading.value = true;
    for (const file of files) {
        const formData = new FormData();
        formData.append('file', file);
        if (currentFolderId.value) {
            formData.append('folder_id', currentFolderId.value);
        }
        try {
            await axios.post(route('admin.media.api.assets.store'), formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        } catch (error) {
            console.error('Upload failed for file:', file.name, error);
            alert('Failed to upload ' + file.name);
        }
    }
    uploading.value = false;
    fileInput.value.value = ''; // Reset
    fetchMedia(currentFolderId.value);
};

const formatBytes = (bytes, decimals = 2) => {
    if (!+bytes) return '0 Bytes';
    const k = 1024, dm = decimals < 0 ? 0 : decimals, sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'], i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
};

</script>

<template>
    <AdminLayout>
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Thư viện Media</h1>
                <p class="text-sm text-gray-500 mt-2 font-medium">Quản lý toàn bộ hình ảnh và tệp tin của hệ thống.</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="showCreateFolderModal = true" class="flex items-center gap-2 bg-white text-gray-900 border border-gray-200 px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-colors shadow-sm">
                    <FolderPlus class="w-4 h-4" />
                    Thư mục mới
                </button>
                <button @click="triggerFileUpload" class="flex items-center gap-2 bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-amber-600 transition-colors shadow-sm" :disabled="uploading">
                    <Upload class="w-4 h-4" />
                    {{ uploading ? 'Đang tải lên...' : 'Tải tệp lên' }}
                </button>
                <input type="file" ref="fileInput" class="hidden" multiple accept="image/*" @change="handleFileUpload" />
            </div>
        </div>

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 mb-6 p-4 bg-white rounded-xl shadow-sm border border-gray-100 font-medium text-sm text-gray-600">
            <button @click="handleNavigate(null)" class="hover:text-amber-600 flex items-center gap-1">
                <Home class="w-4 h-4" /> Media Gốc
            </button>
            <template v-for="(b, i) in breadcrumbs" :key="b.id">
                <ChevronRight class="w-4 h-4 text-gray-300" />
                <button @click="handleNavigate(b.id)" class="hover:text-amber-600 truncate max-w-[150px]">{{ b.name }}</button>
            </template>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 min-h-[60vh] relative"
             @dragover.prevent="isDragging = true"
             @dragleave.prevent="isDragging = false"
             @drop.prevent="handleDrop"
             :class="{'border-amber-400 bg-amber-50/50': isDragging}">
            
            <div v-if="isDragging" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl border-2 border-dashed border-amber-400">
                <div class="text-center">
                    <Upload class="w-12 h-12 text-amber-500 mx-auto mb-3" />
                    <h3 class="text-xl font-bold text-gray-900">Thả tệp vào đây để tải lên</h3>
                </div>
            </div>

            <div v-if="loading" class="flex items-center justify-center h-48">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-500"></div>
            </div>

            <div v-else>
                <h3 v-if="folders.length > 0" class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b pb-2">Thư mục</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
                    <div v-for="folder in folders" :key="folder.id" class="group relative bg-gray-50 border border-gray-200 rounded-xl p-4 hover:shadow-md hover:border-amber-300 transition-all cursor-pointer flex flex-col items-center justify-center text-center">
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click.stop="deleteFolder(folder)" class="p-1 text-red-400 hover:text-red-600 bg-white rounded-md shadow-sm border border-gray-100">
                                <Trash2 class="w-3.5 h-3.5" />
                            </button>
                        </div>
                        <div @click="handleNavigate(folder.id)">
                            <Folder class="w-10 h-10 text-gray-400 group-hover:text-amber-500 mb-2 transition-colors mx-auto" />
                            <span class="text-sm font-semibold text-gray-700 truncate w-full block px-2">{{ folder.name }}</span>
                        </div>
                    </div>
                </div>

                <h3 v-if="assets.length > 0 || folders.length === 0" class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b pb-2">Tệp tin</h3>
                
                <div v-if="assets.length === 0 && folders.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400">
                    <ImageIcon class="w-16 h-16 mb-4 opacity-50" />
                    <p class="text-lg font-medium text-gray-500">Thư mục này đang trống</p>
                    <p class="text-sm mt-1">Kéo và thả tệp vào đây để tải lên</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div v-for="asset in assets" :key="asset.id" class="group relative bg-gray-50 border border-gray-200 rounded-xl overflow-hidden hover:shadow-md hover:border-amber-300 transition-all h-40 flex flex-col">
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                            <button @click.stop="deleteAsset(asset)" class="p-1.5 text-red-500 hover:text-red-700 bg-white/90 backdrop-blur-sm rounded-md shadow-sm border border-gray-100">
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                        <div class="h-28 bg-gray-100 flex items-center justify-center overflow-hidden">
                            <img v-if="asset.mime_type.startsWith('image/')" :src="asset.url" class="w-full h-full object-cover" />
                            <ImageIcon v-else class="w-8 h-8 text-gray-400" />
                        </div>
                        <div class="p-3 bg-white flex-1 border-t border-gray-100 flex flex-col justify-center">
                            <span class="text-xs font-bold text-gray-700 truncate block">{{ asset.original_name }}</span>
                            <span class="text-[10px] text-gray-400 font-mono mt-0.5">{{ formatBytes(asset.size_bytes) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Folder Modal -->
        <div v-if="showCreateFolderModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 w-full max-w-sm">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Thư mục mới</h3>
                    <button @click="showCreateFolderModal = false" class="text-gray-400 hover:text-gray-900">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên thư mục</label>
                    <input v-model="newFolderName" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="VD: Sản phẩm, Banner..." @keyup.enter="createFolder" />
                </div>
                <div class="flex gap-3 justify-end">
                    <button @click="showCreateFolderModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button @click="createFolder" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold hover:bg-amber-600 transition-colors">Tạo thư mục</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
