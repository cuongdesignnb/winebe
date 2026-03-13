<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import { Folder, Image as ImageIcon, X, ChevronRight, Upload, Home } from 'lucide-vue-next';

const props = defineProps({
    modelValue: { type: [Number, String, Array], default: null },
    multiple: { type: Boolean, default: false },
    initialAssets: { type: Array, default: () => [] }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const currentFolderId = ref(null);
const folders = ref([]);
const assets = ref([]);
const breadcrumbs = ref([]);
const loading = ref(false);
const fileInput = ref(null);
const uploading = ref(false);

const localSelected = ref(props.multiple ? (Array.isArray(props.modelValue) ? [...props.modelValue] : []) : props.modelValue);
const selectedAssets = ref([...props.initialAssets]);

onMounted(() => {
    // If we have initial assets but localSelected is empty (shouldn't happen on edit), or vice-versa
    if (props.initialAssets.length > 0 && selectedAssets.value.length === 0) {
        selectedAssets.value = [...props.initialAssets];
    }
});

// We need to fetch/match full asset objects to show thumbnails in the trigger area
watch(() => props.modelValue, (newVal) => {
    localSelected.value = props.multiple ? (Array.isArray(newVal) ? [...newVal] : []) : newVal;
}, { deep: true });

// When assets are fetched, if some are selected but not in selectedAssets, we could potentially add them
// but for simplicity, let's just track them during selection

const openModal = () => {
    isOpen.value = true;
    if (folders.value.length === 0 && assets.value.length === 0) fetchMedia();
};

const closeModal = () => {
    isOpen.value = false;
};

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

const navigate = (folderId) => fetchMedia(folderId);

const toggleSelection = (asset) => {
    if (props.multiple) {
        const index = localSelected.value.indexOf(asset.id);
        if (index > -1) {
            localSelected.value.splice(index, 1);
            selectedAssets.value = selectedAssets.value.filter(a => a.id !== asset.id);
        } else {
            localSelected.value.push(asset.id);
            selectedAssets.value.push(asset);
        }
    } else {
        if (localSelected.value === asset.id) {
            localSelected.value = null;
            selectedAssets.value = [];
        } else {
            localSelected.value = asset.id;
            selectedAssets.value = [asset];
        }
    }
};

const isSelected = (asset) => {
    if (props.multiple) return localSelected.value.includes(asset.id);
    return localSelected.value === asset.id;
};

const confirmSelection = () => {
    emit('update:modelValue', localSelected.value);
    closeModal();
};

const removeSelected = (id) => {
    if (props.multiple) {
        localSelected.value = localSelected.value.filter(i => i !== id);
        selectedAssets.value = selectedAssets.value.filter(a => a.id !== id);
    } else {
        localSelected.value = null;
        selectedAssets.value = [];
    }
    emit('update:modelValue', localSelected.value);
};

const triggerFileUpload = () => fileInput.value.click();

const handleFileUpload = async (event) => {
    const files = event.target.files;
    if (!files.length) return;
    
    uploading.value = true;
    for (const file of files) {
        const formData = new FormData();
        formData.append('file', file);
        if (currentFolderId.value) formData.append('folder_id', currentFolderId.value);
        try {
            await axios.post(route('admin.media.api.assets.store'), formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        } catch (error) {
            console.error('Upload failed', error);
        }
    }
    uploading.value = false;
    fileInput.value.value = '';
    fetchMedia(currentFolderId.value);
};
</script>

<template>
    <div class="space-y-4">
        <!-- Gallery Preview Area -->
        <div v-if="localSelected && (multiple ? localSelected.length > 0 : localSelected)" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div v-for="id in (multiple ? localSelected : [localSelected])" :key="id" class="relative group aspect-square rounded-xl overflow-hidden border border-gray-100 shadow-sm bg-gray-50 flex items-center justify-center">
                <!-- We try to find the URL from selectedAssets or potentially just show a placeholder if not loaded yet -->
                <img :src="selectedAssets.find(a => a.id === id)?.url || '/images/bottle-placeholder.png'" class="w-full h-full object-cover" />
                <button @click.stop="removeSelected(id)" class="absolute top-1 right-1 bg-white/90 text-gray-400 hover:text-red-500 p-1.5 rounded-full shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
                    <X class="w-3.5 h-3.5" />
                </button>
                <div v-if="!selectedAssets.find(a => a.id === id)" class="absolute inset-0 flex items-center justify-center bg-gray-900/10 backdrop-blur-[2px]">
                   <span class="text-[10px] font-bold text-gray-700 bg-white/80 px-2 py-1 rounded">ID: {{ id }}</span>
                </div>
            </div>
            
            <!-- Small Trigger Button inside grid -->
            <div @click="openModal" class="aspect-square border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center cursor-pointer hover:border-amber-400 hover:bg-amber-50 transition-all text-gray-400">
                <Plus class="w-6 h-6 mb-1" />
                <span class="text-[10px] font-bold uppercase tracking-wider">Thêm ảnh</span>
            </div>
        </div>

        <!-- Empty State / Main Trigger -->
        <div v-else @click="openModal" class="border-2 border-dashed border-gray-300 rounded-2xl p-8 flex flex-col items-center justify-center text-center cursor-pointer hover:border-amber-400 hover:bg-amber-50 transition-all bg-gray-50 h-[180px] group">
            <div class="w-14 h-14 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center text-gray-400 group-hover:text-amber-500 group-hover:scale-110 transition-all mb-4">
                <ImageIcon class="w-7 h-7" />
            </div>
            <span class="text-sm font-bold text-gray-900">Chọn hình ảnh sản phẩm</span>
            <p class="text-xs text-gray-400 mt-2">Kéo thả hoặc click để mở thư viện</p>
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" style="margin: 0;">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeModal"></div>
                
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden z-10 mx-auto">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50 shrink-0">
                        <h2 class="text-lg font-bold text-gray-900">Select Media</h2>
                        <button @click="closeModal" class="p-2 text-gray-400 hover:text-gray-900 rounded-lg transition-colors">
                            <X class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Toolbar -->
                    <div class="px-6 py-3 flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-gray-100 bg-white shrink-0">
                        <div class="flex items-center gap-2 text-sm font-medium text-gray-600">
                            <button @click="navigate(null)" class="hover:text-amber-600 flex items-center gap-1"><Home class="w-4 h-4" /> Root</button>
                            <template v-for="b in breadcrumbs" :key="b.id">
                                <ChevronRight class="w-4 h-4 text-gray-300" />
                                <button @click="navigate(b.id)" class="hover:text-amber-600 max-w-[100px] truncate">{{ b.name }}</button>
                            </template>
                        </div>

                        <button @click="triggerFileUpload" class="flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-amber-600 transition-colors shadow-sm" :disabled="uploading">
                            <Upload class="w-4 h-4" />
                            {{ uploading ? 'Uploading...' : 'Upload Here' }}
                        </button>
                        <input type="file" ref="fileInput" class="hidden" multiple accept="image/*" @change="handleFileUpload" />
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-6 bg-gray-50/50 min-h-0">
                        <div v-if="loading" class="flex justify-center py-12">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-500"></div>
                        </div>
                        <div v-else>
                            <!-- Folders -->
                            <div v-if="folders.length > 0" class="mb-4">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 border-b pb-1">Folders</h3>
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                                    <div v-for="folder in folders" :key="folder.id" @click="navigate(folder.id)" class="bg-white border border-gray-200 rounded-lg p-3 hover:border-amber-400 hover:shadow-sm transition-all cursor-pointer flex flex-col items-center text-center">
                                        <Folder class="w-8 h-8 text-gray-400 mb-1" />
                                        <span class="text-[11px] font-bold text-gray-700 truncate w-full px-1">{{ folder.name }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Assets -->
                            <div v-if="assets.length > 0">
                                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 border-b pb-1">Images</h3>
                                <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8 gap-3">
                                    <div v-for="asset in assets" :key="asset.id" @click="toggleSelection(asset)" class="relative bg-white border-2 rounded-lg overflow-hidden cursor-pointer transition-all aspect-square flex flex-col" :class="isSelected(asset) ? 'border-amber-500 shadow-sm ring-2 ring-amber-500/20' : 'border-transparent border-gray-200 hover:border-amber-300 hover:shadow-sm'">
                                        <div v-if="isSelected(asset)" class="absolute top-1.5 right-1.5 bg-amber-500 text-white w-5 h-5 rounded-full flex items-center justify-center shadow-sm z-20 text-[10px] font-bold border-2 border-white">✓</div>
                                        <div class="flex-1 bg-gray-100 flex items-center justify-center overflow-hidden w-full h-full">
                                            <img v-if="asset.mime_type?.startsWith('image/')" :src="asset.url" class="min-w-full min-h-full object-cover" />
                                            <ImageIcon v-else class="w-8 h-8 text-gray-300" />
                                        </div>
                                        <div class="h-6 bg-white flex items-center justify-center px-1 border-t border-gray-100 shrink-0">
                                            <p class="text-[9px] text-gray-500 font-medium truncate w-full text-center">{{ asset.original_name }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="folders.length === 0 && assets.length === 0" class="text-center py-20 text-gray-400">
                                <ImageIcon class="w-12 h-12 mx-auto mb-3 opacity-30" />
                                <p class="text-sm font-medium">No media found in this folder.</p>
                                <button @click="triggerFileUpload" class="mt-4 text-xs font-bold text-amber-600 hover:text-amber-700 uppercase tracking-widest bg-amber-50 px-4 py-2 rounded-lg border border-amber-100">Click to upload here</button>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-4 bg-white border-t border-gray-100 flex justify-between items-center z-20 shrink-0">
                        <div class="text-sm text-gray-500 font-medium whitespace-nowrap">
                            <span v-if="multiple">{{ localSelected.length }} items selected</span>
                            <span v-else-if="localSelected">1 item selected</span>
                            <span v-else>0 selected</span>
                        </div>
                        <div class="flex gap-2">
                            <button @click="closeModal" class="px-5 py-2 text-sm font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Cancel</button>
                            <button @click="confirmSelection" class="px-6 py-2 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-amber-600 transition-colors shadow-sm">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>
