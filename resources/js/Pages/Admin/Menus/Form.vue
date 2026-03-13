<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import { Plus, GripVertical, ChevronDown, ChevronRight, Pencil, Trash2, Eye, EyeOff, ExternalLink, X, Save } from 'lucide-vue-next';

const props = defineProps({
    menu: { type: Object, default: null },
    menuItems: { type: Array, default: () => [] },
});

const isEdit = !!props.menu;

const form = useForm({
    name: props.menu?.name || '',
    code: props.menu?.code || '',
    location: props.menu?.location || '',
    is_active: props.menu?.is_active ?? true,
});

const submit = () => {
    if (isEdit) form.put(route('admin.menus.update', props.menu.id));
    else form.post(route('admin.menus.store'));
};

// ---- Menu items management (only when editing) ----
const items = ref(props.menuItems || []);
const showAddItemModal = ref(false);
const showEditItemModal = ref(false);
const expandedItems = ref(initExpandedItems());

// Auto-expand all items that have children
function initExpandedItems() {
    const expanded = {};
    function expandRecursive(list) {
        for (const item of list) {
            if (item.children_list && item.children_list.length > 0) {
                expanded[item.id] = true;
                expandRecursive(item.children_list);
            }
        }
    }
    expandRecursive(props.menuItems || []);
    return expanded;
}

const newItem = ref({
    title: '',
    item_type: 'custom_link',
    url: '',
    parent_id: null,
    is_visible: true,
    open_in_new_tab: false,
    badge_text: '',
    icon_name: '',
    css_class: '',
});

const editingItem = ref(null);

const toggleExpand = (id) => {
    expandedItems.value[id] = !expandedItems.value[id];
};

const openAddModal = (parentId = null) => {
    newItem.value = {
        title: '', item_type: 'custom_link', url: '', parent_id: parentId,
        is_visible: true, open_in_new_tab: false, badge_text: '', icon_name: '', css_class: '',
    };
    showAddItemModal.value = true;
};

const addItem = async () => {
    if (!newItem.value.title) return;
    try {
        const res = await axios.post(route('admin.menus.items.store', props.menu.id), newItem.value);
        showAddItemModal.value = false;
        refreshItems();
    } catch (error) {
        console.error('Failed to add item:', error);
        alert(error.response?.data?.message || 'Failed to add item.');
    }
};

const openEditModal = (item) => {
    editingItem.value = { ...item };
    showEditItemModal.value = true;
};

const saveEditItem = async () => {
    try {
        await axios.put(route('admin.menus.items.update', { menu: props.menu.id, item: editingItem.value.id }), editingItem.value);
        showEditItemModal.value = false;
        refreshItems();
    } catch (error) {
        console.error('Failed to update item:', error);
    }
};

const deleteItem = async (item) => {
    if (!confirm(`Delete "${item.title}"?`)) return;
    try {
        await axios.delete(route('admin.menus.items.destroy', { menu: props.menu.id, item: item.id }));
        refreshItems();
    } catch (error) {
        console.error('Failed to delete item:', error);
    }
};

const refreshItems = async () => {
    // re-fetch via Inertia partial reload
    window.location.reload();
};

const itemTypeLabels = {
    custom_link: 'Link tùy chỉnh',
    category: 'Danh mục',
    brand: 'Thương hiệu',
    page: 'Trang CMS',
};
</script>

<template>
    <AdminLayout>
        <div class="mb-6">
            <Link :href="route('admin.menus.index')" class="text-sm text-gray-500 hover:text-gray-900">← Quay lại danh sách</Link>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ isEdit ? 'Chỉnh sửa Menu' : 'Thêm Menu mới' }}</h1>
        </div>

        <div class="max-w-4xl space-y-6">
            <!-- Menu Details -->
            <form @submit.prevent="submit">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                    <h2 class="font-semibold text-gray-900 border-b pb-3">Chi tiết Menu</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên Menu *</label>
                            <input v-model="form.name" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="VD: Menu chính, Menu chân trang..." />
                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mã định danh (unique code) *</label>
                            <input v-model="form.code" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-amber-400 outline-none" placeholder="VD: main-nav" />
                            <div v-if="form.errors.code" class="text-red-500 text-xs mt-1">{{ form.errors.code }}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vị trí hiển thị</label>
                            <select v-model="form.location" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                                <option value="">— Không chọn —</option>
                                <option value="header">Đầu trang (Header)</option>
                                <option value="footer">Chân trang (Footer)</option>
                                <option value="sidebar">Thanh bên (Sidebar)</option>
                                <option value="mobile">Di động (Mobile)</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 text-sm pb-2.5 cursor-pointer">
                                <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-amber-500 focus:ring-amber-400" />
                                <span class="font-medium text-gray-700">Kích hoạt</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" :disabled="form.processing" class="bg-gray-900 text-white px-8 py-3.5 rounded-xl text-sm font-bold hover:bg-amber-600 transition-colors disabled:opacity-50 shadow-md">
                            <Save class="w-4 h-4 inline mr-1" />
                            {{ form.processing ? 'Đang lưu...' : (isEdit ? 'Cập nhật Menu' : 'Tạo Menu') }}
                        </button>
                        <Link :href="route('admin.menus.index')" class="px-8 py-3.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">Hủy</Link>
                    </div>
                </div>
            </form>

            <!-- Menu Items Builder (only when editing) -->
            <div v-if="isEdit" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-semibold text-gray-900">Các mục Menu</h2>
                    <button @click="openAddModal(null)" class="flex items-center gap-2 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-amber-600 transition-colors">
                        <Plus class="w-4 h-4" /> Thêm mục mới
                    </button>
                </div>

                <div v-if="items.length === 0" class="text-center py-12 text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
                    <GripVertical class="w-10 h-10 mx-auto mb-3 opacity-30" />
                    <p class="text-sm font-medium">Chưa có mục nào. Hãy bắt đầu thêm mục đầu tiên.</p>
                </div>

                <!-- Recursive tree rendering -->
                <div v-else class="space-y-1">
                    <template v-for="item in items" :key="item.id">
                        <div class="border border-gray-100 rounded-lg overflow-hidden transition-all hover:border-amber-200">
                            <div class="flex items-center gap-3 px-4 py-3 bg-amber-50/60 hover:bg-amber-50 transition-colors">
                                <GripVertical class="w-4 h-4 text-gray-300 cursor-grab flex-shrink-0" />
                                
                                <button v-if="item.children_list && item.children_list.length > 0" @click="toggleExpand(item.id)" class="text-gray-400 hover:text-gray-700">
                                    <ChevronDown v-if="expandedItems[item.id]" class="w-4 h-4" />
                                    <ChevronRight v-else class="w-4 h-4" />
                                </button>
                                <div v-else class="w-4"></div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-bold text-amber-700 bg-amber-200 px-1.5 py-0.5 rounded">CẤP 1</span>
                                        <span class="font-bold text-sm text-gray-900">{{ item.title }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] font-mono text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">{{ itemTypeLabels[item.item_type] || item.item_type }}</span>
                                        <span v-if="item.url" class="text-[10px] text-gray-400 truncate max-w-[200px]">{{ item.url }}</span>
                                        <span v-if="item.badge_text" class="text-[10px] bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded font-bold">{{ item.badge_text }}</span>
                                        <span v-if="item.children_list && item.children_list.length > 0" class="text-[9px] text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded font-semibold">{{ item.children_list.length }} mục con → Mega Menu</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <span v-if="!item.is_visible" class="text-gray-300"><EyeOff class="w-4 h-4" /></span>
                                    <span v-if="item.open_in_new_tab" class="text-gray-300"><ExternalLink class="w-3.5 h-3.5" /></span>

                                    <button @click="openAddModal(item.id)" class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-md transition-colors" title="Thêm mục cấp 2">
                                        <Plus class="w-4 h-4" />
                                    </button>
                                    <button @click="openEditModal(item)" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-md transition-colors">
                                        <Pencil class="w-4 h-4" />
                                    </button>
                                    <button @click="deleteItem(item)" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors">
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>

                            <!-- Children Level 2 (Mega Menu columns) -->
                            <div v-if="item.children_list && item.children_list.length > 0 && expandedItems[item.id]" class="pl-8 border-t border-gray-100 bg-white">
                                <div v-for="child in item.children_list" :key="child.id" class="border-b border-gray-50 last:border-none hover:bg-amber-50/20 transition-colors">
                                    <div class="flex items-center gap-3 px-4 py-2.5">
                                        <GripVertical class="w-4 h-4 text-gray-200 cursor-grab flex-shrink-0" />
                                        <div class="w-4 h-4 border-l-2 border-b-2 border-gray-200 rounded-bl-md flex-shrink-0 -mt-3"></div>
                                        
                                        <button v-if="child.children_list && child.children_list.length > 0" @click="toggleExpand(child.id)" class="text-gray-400 hover:text-gray-700">
                                            <ChevronDown v-if="expandedItems[child.id]" class="w-3.5 h-3.5" />
                                            <ChevronRight v-else class="w-3.5 h-3.5" />
                                        </button>
                                        <div v-else class="w-3.5"></div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[9px] font-bold text-blue-700 bg-blue-100 px-1.5 py-0.5 rounded">CẤP 2</span>
                                                <span class="font-semibold text-sm text-gray-800">{{ child.title }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-mono text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">{{ itemTypeLabels[child.item_type] || child.item_type }}</span>
                                                <span v-if="child.url" class="text-[10px] text-gray-400 truncate max-w-[150px]">{{ child.url }}</span>
                                                <span v-if="child.children_list && child.children_list.length > 0" class="text-[9px] text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded font-semibold">{{ child.children_list.length }} links</span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-1 flex-shrink-0">
                                            <button @click="openAddModal(child.id)" class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-md transition-colors" title="Thêm mục cấp 3">
                                                <Plus class="w-3.5 h-3.5" />
                                            </button>
                                            <button @click="openEditModal(child)" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-md transition-colors">
                                                <Pencil class="w-3.5 h-3.5" />
                                            </button>
                                            <button @click="deleteItem(child)" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors">
                                                <Trash2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Children Level 3 (Links in mega menu) -->
                                    <div v-if="child.children_list && child.children_list.length > 0 && expandedItems[child.id]" class="pl-12 bg-gray-50/30">
                                        <div v-for="grandchild in child.children_list" :key="grandchild.id" class="flex items-center gap-3 px-4 py-2 border-b border-gray-50 last:border-none hover:bg-amber-50/50 transition-colors">
                                            <GripVertical class="w-3.5 h-3.5 text-gray-200 cursor-grab flex-shrink-0" />
                                            <div class="w-4 h-4 border-l-2 border-b-2 border-amber-200 rounded-bl-md flex-shrink-0 -mt-3"></div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[9px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded">CẤP 3</span>
                                                    <span class="font-medium text-[13px] text-gray-700">{{ grandchild.title }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[9px] font-mono text-gray-400">{{ grandchild.url }}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1 flex-shrink-0">
                                                <button @click="openEditModal(grandchild)" class="p-1 text-gray-400 hover:text-amber-600 transition-colors">
                                                    <Pencil class="w-3 h-3" />
                                                </button>
                                                <button @click="deleteItem(grandchild)" class="p-1 text-gray-400 hover:text-red-600 transition-colors">
                                                    <Trash2 class="w-3 h-3" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Add Item Modal -->
        <Teleport to="body">
            <div v-if="showAddItemModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="margin:0">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showAddItemModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-gray-900">{{ newItem.parent_id ? 'Thêm mục con' : 'Thêm mục menu' }}</h3>
                        <button @click="showAddItemModal = false" class="text-gray-400 hover:text-gray-900"><X class="w-5 h-5" /></button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề (Hiển thị) *</label>
                            <input v-model="newItem.title" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="VD: Về chúng tôi..." @keyup.enter="addItem" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loại liên kết</label>
                            <select v-model="newItem.item_type" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                                <option value="custom_link">Link tùy chỉnh</option>
                                <option value="category">Danh mục</option>
                                <option value="brand">Thương hiệu</option>
                                <option value="page">Trang CMS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Đường dẫn (URL)</label>
                            <input v-model="newItem.url" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="VD: /ve-chung-toi hoặc https://..." />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chữ nhãn (Badge)</label>
                                <input v-model="newItem.badge_text" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="VD: MỚI, HOT..." />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên Icon (Lucide)</label>
                                <input v-model="newItem.icon_name" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="VD: home, wine..." />
                            </div>
                        </div>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 text-sm cursor-pointer"><input v-model="newItem.is_visible" type="checkbox" class="rounded border-gray-300 text-amber-500" /> Hiển thị</label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer"><input v-model="newItem.open_in_new_tab" type="checkbox" class="rounded border-gray-300 text-amber-500" /> Mở tab mới</label>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button @click="showAddItemModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-lg">Hủy</button>
                        <button @click="addItem" class="px-6 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold hover:bg-amber-600 transition-colors">Thêm ngay</button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Edit Item Modal -->
        <Teleport to="body">
            <div v-if="showEditItemModal && editingItem" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="margin:0">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showEditItemModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md z-10 p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-gray-900">Chỉnh sửa mục</h3>
                        <button @click="showEditItemModal = false" class="text-gray-400 hover:text-gray-900"><X class="w-5 h-5" /></button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề *</label>
                            <input v-model="editingItem.title" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Loại liên kết</label>
                            <select v-model="editingItem.item_type" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none">
                                <option value="custom_link">Link tùy chỉnh</option>
                                <option value="category">Danh mục</option>
                                <option value="brand">Thương hiệu</option>
                                <option value="page">Trang CMS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Đường dẫn (URL)</label>
                            <input v-model="editingItem.url" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Chữ nhãn (Badge)</label>
                                <input v-model="editingItem.badge_text" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên Icon</label>
                                <input v-model="editingItem.icon_name" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CSS Class (Tùy chọn)</label>
                            <input v-model="editingItem.css_class" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" />
                        </div>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 text-sm cursor-pointer"><input v-model="editingItem.is_visible" type="checkbox" class="rounded border-gray-300 text-amber-500" /> Hiển thị</label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer"><input v-model="editingItem.open_in_new_tab" type="checkbox" class="rounded border-gray-300 text-amber-500" /> Mở tab mới</label>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button @click="showEditItemModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-lg">Hủy</button>
                        <button @click="saveEditItem" class="px-6 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold hover:bg-amber-600 transition-colors">Lưu thay đổi</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
