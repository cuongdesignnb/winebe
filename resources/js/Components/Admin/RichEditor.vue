<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import TextAlign from '@tiptap/extension-text-align';
import Placeholder from '@tiptap/extension-placeholder';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import Highlight from '@tiptap/extension-highlight';
import Typography from '@tiptap/extension-typography';
import { watch, onBeforeUnmount, ref } from 'vue';
import {
    Bold, Italic, Underline as UnderlineIcon, Strikethrough,
    List, ListOrdered, Quote, Code, Heading1, Heading2, Heading3,
    AlignLeft, AlignCenter, AlignRight, AlignJustify,
    Link as LinkIcon, Image as ImageIcon, Highlighter, Undo2, Redo2,
    Minus, RemoveFormatting, Pilcrow
} from 'lucide-vue-next';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Start writing...' },
    minHeight: { type: String, default: '280px' },
});

const emit = defineEmits(['update:modelValue']);
const showLinkModal = ref(false);
const linkUrl = ref('');

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit.configure({
            heading: { levels: [1, 2, 3, 4] },
        }),
        Underline,
        TextAlign.configure({ types: ['heading', 'paragraph'] }),
        Placeholder.configure({ placeholder: props.placeholder }),
        Link.configure({ openOnClick: false, HTMLAttributes: { class: 'text-amber-600 underline' } }),
        Image.configure({ inline: true }),
        Highlight.configure({ multicolor: false }),
        Typography,
    ],
    editorProps: {
        attributes: {
            class: 'prose prose-sm max-w-none focus:outline-none',
            style: `min-height: ${props.minHeight}; padding: 1rem;`,
        },
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

watch(() => props.modelValue, (newVal) => {
    if (editor.value && editor.value.getHTML() !== newVal) {
        editor.value.commands.setContent(newVal, false);
    }
});

onBeforeUnmount(() => {
    editor.value?.destroy();
});

const setLink = () => {
    if (!linkUrl.value) {
        editor.value?.chain().focus().extendMarkRange('link').unsetLink().run();
    } else {
        let url = linkUrl.value;
        if (!/^https?:\/\//.test(url) && !url.startsWith('/')) url = 'https://' + url;
        editor.value?.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
    }
    showLinkModal.value = false;
    linkUrl.value = '';
};

const openLinkModal = () => {
    const prev = editor.value?.getAttributes('link')?.href || '';
    linkUrl.value = prev;
    showLinkModal.value = true;
};

const addImage = () => {
    const url = prompt('Enter image URL:');
    if (url) editor.value?.chain().focus().setImage({ src: url }).run();
};

const isActive = (name, attrs = {}) => editor.value?.isActive(name, attrs) ?? false;
</script>

<template>
    <div class="rich-editor border border-gray-200 rounded-xl overflow-hidden bg-white">
        <!-- Toolbar -->
        <div v-if="editor" class="border-b border-gray-100 bg-gray-50/80 px-2 py-1.5 flex flex-wrap items-center gap-0.5">
            <!-- Undo/Redo -->
            <button type="button" @click="editor.chain().focus().undo().run()" :disabled="!editor.can().undo()" class="tb-btn" title="Undo">
                <Undo2 class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().redo().run()" :disabled="!editor.can().redo()" class="tb-btn" title="Redo">
                <Redo2 class="w-4 h-4" />
            </button>

            <span class="tb-sep"></span>

            <!-- Headings -->
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="{ 'tb-active': isActive('heading', { level: 1 }) }" class="tb-btn" title="Heading 1">
                <Heading1 class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'tb-active': isActive('heading', { level: 2 }) }" class="tb-btn" title="Heading 2">
                <Heading2 class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'tb-active': isActive('heading', { level: 3 }) }" class="tb-btn" title="Heading 3">
                <Heading3 class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().setParagraph().run()" :class="{ 'tb-active': isActive('paragraph') }" class="tb-btn" title="Paragraph">
                <Pilcrow class="w-4 h-4" />
            </button>

            <span class="tb-sep"></span>

            <!-- Text Formatting -->
            <button type="button" @click="editor.chain().focus().toggleBold().run()" :class="{ 'tb-active': isActive('bold') }" class="tb-btn" title="Bold">
                <Bold class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleItalic().run()" :class="{ 'tb-active': isActive('italic') }" class="tb-btn" title="Italic">
                <Italic class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleUnderline().run()" :class="{ 'tb-active': isActive('underline') }" class="tb-btn" title="Underline">
                <UnderlineIcon class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleStrike().run()" :class="{ 'tb-active': isActive('strike') }" class="tb-btn" title="Strikethrough">
                <Strikethrough class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleHighlight().run()" :class="{ 'tb-active': isActive('highlight') }" class="tb-btn" title="Highlight">
                <Highlighter class="w-4 h-4" />
            </button>

            <span class="tb-sep"></span>

            <!-- Alignment -->
            <button type="button" @click="editor.chain().focus().setTextAlign('left').run()" :class="{ 'tb-active': isActive({ textAlign: 'left' }) }" class="tb-btn" title="Align Left">
                <AlignLeft class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().setTextAlign('center').run()" :class="{ 'tb-active': isActive({ textAlign: 'center' }) }" class="tb-btn" title="Align Center">
                <AlignCenter class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().setTextAlign('right').run()" :class="{ 'tb-active': isActive({ textAlign: 'right' }) }" class="tb-btn" title="Align Right">
                <AlignRight class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().setTextAlign('justify').run()" :class="{ 'tb-active': isActive({ textAlign: 'justify' }) }" class="tb-btn" title="Justify">
                <AlignJustify class="w-4 h-4" />
            </button>

            <span class="tb-sep"></span>

            <!-- Lists -->
            <button type="button" @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'tb-active': isActive('bulletList') }" class="tb-btn" title="Bullet List">
                <List class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'tb-active': isActive('orderedList') }" class="tb-btn" title="Numbered List">
                <ListOrdered class="w-4 h-4" />
            </button>

            <span class="tb-sep"></span>

            <!-- Block elements -->
            <button type="button" @click="editor.chain().focus().toggleBlockquote().run()" :class="{ 'tb-active': isActive('blockquote') }" class="tb-btn" title="Quote">
                <Quote class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().toggleCodeBlock().run()" :class="{ 'tb-active': isActive('codeBlock') }" class="tb-btn" title="Code Block">
                <Code class="w-4 h-4" />
            </button>
            <button type="button" @click="editor.chain().focus().setHorizontalRule().run()" class="tb-btn" title="Horizontal Rule">
                <Minus class="w-4 h-4" />
            </button>

            <span class="tb-sep"></span>

            <!-- Link & Image -->
            <button type="button" @click="openLinkModal" :class="{ 'tb-active': isActive('link') }" class="tb-btn" title="Insert Link">
                <LinkIcon class="w-4 h-4" />
            </button>
            <button type="button" @click="addImage" class="tb-btn" title="Insert Image">
                <ImageIcon class="w-4 h-4" />
            </button>

            <span class="tb-sep"></span>

            <!-- Clear Formatting -->
            <button type="button" @click="editor.chain().focus().clearNodes().unsetAllMarks().run()" class="tb-btn" title="Clear Formatting">
                <RemoveFormatting class="w-4 h-4" />
            </button>
        </div>

        <!-- Editor Content -->
        <EditorContent :editor="editor" class="editor-body" />

        <!-- Link Modal -->
        <Teleport to="body">
            <div v-if="showLinkModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="margin:0">
                <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="showLinkModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-10 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Insert Link</h3>
                    <input v-model="linkUrl" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 outline-none" placeholder="https://example.com" @keyup.enter="setLink" autofocus />
                    <div class="flex gap-3 justify-end mt-4">
                        <button type="button" @click="showLinkModal = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                        <button type="button" @click="() => { editor?.chain().focus().extendMarkRange('link').unsetLink().run(); showLinkModal = false; }" class="px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 rounded-lg">Remove</button>
                        <button type="button" @click="setLink" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold hover:bg-amber-600 transition-colors">Apply</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
.tb-btn {
    @apply p-1.5 rounded-md text-gray-500 hover:text-gray-900 hover:bg-white transition-all duration-150 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer;
}
.tb-active {
    @apply bg-amber-100 text-amber-700 shadow-sm;
}
.tb-sep {
    @apply w-px h-5 bg-gray-200 mx-1;
}

.editor-body :deep(.tiptap) {
    min-height: v-bind(minHeight);
    padding: 1rem;
    outline: none;
}
.editor-body :deep(.tiptap p.is-editor-empty:first-child::before) {
    content: attr(data-placeholder);
    float: left;
    color: #9ca3af;
    pointer-events: none;
    height: 0;
}
.editor-body :deep(.tiptap h1) { font-size: 1.75rem; font-weight: 800; margin: 1rem 0 0.5rem; line-height: 1.3; }
.editor-body :deep(.tiptap h2) { font-size: 1.4rem; font-weight: 700; margin: 0.8rem 0 0.4rem; line-height: 1.35; }
.editor-body :deep(.tiptap h3) { font-size: 1.15rem; font-weight: 600; margin: 0.6rem 0 0.3rem; line-height: 1.4; }
.editor-body :deep(.tiptap p) { margin: 0.4rem 0; line-height: 1.7; }
.editor-body :deep(.tiptap ul) { list-style-type: disc; padding-left: 1.5rem; margin: 0.5rem 0; }
.editor-body :deep(.tiptap ol) { list-style-type: decimal; padding-left: 1.5rem; margin: 0.5rem 0; }
.editor-body :deep(.tiptap li) { margin: 0.15rem 0; }
.editor-body :deep(.tiptap blockquote) { border-left: 3px solid #d97706; padding-left: 1rem; margin: 0.75rem 0; color: #6b7280; font-style: italic; }
.editor-body :deep(.tiptap pre) { background: #1e293b; color: #e2e8f0; padding: 1rem; border-radius: 0.5rem; font-family: monospace; font-size: 0.85rem; overflow-x: auto; margin: 0.75rem 0; }
.editor-body :deep(.tiptap code) { background: #f1f5f9; padding: 0.15rem 0.4rem; border-radius: 0.25rem; font-size: 0.85em; color: #c2410c; }
.editor-body :deep(.tiptap pre code) { background: none; padding: 0; color: inherit; }
.editor-body :deep(.tiptap hr) { border: none; border-top: 2px solid #e5e7eb; margin: 1.5rem 0; }
.editor-body :deep(.tiptap a) { color: #d97706; text-decoration: underline; cursor: pointer; }
.editor-body :deep(.tiptap img) { max-width: 100%; height: auto; border-radius: 0.5rem; margin: 0.75rem 0; }
.editor-body :deep(.tiptap mark) { background-color: #fef3c7; padding: 0.1rem 0.2rem; border-radius: 0.15rem; }
.editor-body :deep(.tiptap strong) { font-weight: 700; }
</style>
