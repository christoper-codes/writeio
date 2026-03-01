<script setup lang="ts">
import { Bold, Code, Heading2, Heading3, Image, Italic, Link, List, ListOrdered, Minus, Quote, Strikethrough, Underline } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

const props = defineProps<{
    modelValue: string;
    placeholder?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const editorRef = ref<HTMLDivElement | null>(null);
const is_uploading_image = ref(false);
const image_input_ref = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (editorRef.value && props.modelValue) {
        editorRef.value.innerHTML = props.modelValue;
    }
});

watch(
    () => props.modelValue,
    (val) => {
        if (editorRef.value && editorRef.value.innerHTML !== val) {
            editorRef.value.innerHTML = val ?? '';
        }
    },
);

function onInput() {
    emit('update:modelValue', editorRef.value?.innerHTML ?? '');
}

function execCmd(command: string, value: string | undefined = undefined) {
    editorRef.value?.focus();
    document.execCommand(command, false, value);
    onInput();
}

function formatBlock(tag: string) {
    execCmd('formatBlock', tag);
}

function insertHorizontalRule() {
    execCmd('insertHTML', '<hr class="my-6 border-border" /><p><br></p>');
}

function insertBlockquote() {
    const selection = window.getSelection();
    const selected_text = selection?.toString() ?? '';
    execCmd('insertHTML', `<blockquote class="blog-blockquote">${selected_text || 'Highlighted quote...'}</blockquote><p><br></p>`);
}

function triggerImageUpload() {
    image_input_ref.value?.click();
}

async function handleImageUpload(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;

    is_uploading_image.value = true;

    const form_data = new FormData();
    form_data.append('image', file);

    try {
        const response = await fetch('/media/upload', {
            method: 'POST',
            body: form_data,
            headers: {
                'X-XSRF-TOKEN': getCsrfToken(),
                Accept: 'application/json',
            },
        });

        if (!response.ok) throw new Error(`Upload failed: ${response.status}`);

        const data = await response.json();
        if (data.url) {
            editorRef.value?.focus();
            document.execCommand(
                'insertHTML',
                false,
                `<img src="${data.url}" alt="Blog image" class="blog-image" /><p><br></p>`,
            );
            onInput();
        }
    } catch (err) {
        console.error('Image upload error:', err);
    } finally {
        is_uploading_image.value = false;
        target.value = '';
    }
}

function getCsrfToken(): string {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}

function insertLink() {
    const url = prompt('Enter URL:');
    if (url) execCmd('createLink', url);
}

const toolbar_groups = [
    {
        label: 'Headings',
        actions: [
            { icon: Heading2, label: 'Heading 2', action: () => formatBlock('h2') },
            { icon: Heading3, label: 'Heading 3', action: () => formatBlock('h3') },
        ],
    },
    {
        label: 'Format',
        actions: [
            { icon: Bold, label: 'Bold', action: () => execCmd('bold') },
            { icon: Italic, label: 'Italic', action: () => execCmd('italic') },
            { icon: Underline, label: 'Underline', action: () => execCmd('underline') },
            { icon: Strikethrough, label: 'Strikethrough', action: () => execCmd('strikethrough') },
        ],
    },
    {
        label: 'Blocks',
        actions: [
            { icon: Quote, label: 'Blockquote', action: insertBlockquote },
            { icon: Code, label: 'Code', action: () => execCmd('formatBlock', 'pre') },
            { icon: Minus, label: 'Divider', action: insertHorizontalRule },
        ],
    },
    {
        label: 'Lists',
        actions: [
            { icon: List, label: 'Bullet list', action: () => execCmd('insertUnorderedList') },
            { icon: ListOrdered, label: 'Numbered list', action: () => execCmd('insertOrderedList') },
        ],
    },
    {
        label: 'Insert',
        actions: [
            { icon: Link, label: 'Link', action: insertLink },
            { icon: Image, label: 'Image', action: triggerImageUpload },
        ],
    },
];
</script>

<template>
    <div class="blog-editor overflow-hidden rounded-xl border border-border bg-card shadow-sm">
        <!-- Toolbar -->
        <div
            class="flex flex-wrap gap-0.5 border-b border-border bg-muted/40 px-3 py-2"
        >
            <template v-for="group in toolbar_groups" :key="group.label">
                <div class="flex items-center gap-0.5">
                    <button
                        v-for="btn in group.actions"
                        :key="btn.label"
                        type="button"
                        :title="btn.label"
                        class="flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground focus:outline-none"
                        @click="btn.action"
                    >
                        <component :is="btn.icon" :size="15" />
                    </button>
                </div>
                <div class="mx-1 h-8 w-px bg-border" />
            </template>

            <span v-if="is_uploading_image" class="ml-auto flex items-center gap-1.5 text-xs text-muted-foreground">
                <span class="inline-block h-3 w-3 animate-spin rounded-full border-2 border-muted border-t-primary" />
                Uploading...
            </span>
        </div>

        <!-- Editable area -->
        <div
            ref="editorRef"
            contenteditable="true"
            class="blog-content min-h-[400px] px-6 py-5 text-foreground focus:outline-none"
            :data-placeholder="placeholder ?? 'Start writing your blog post...'"
            @input="onInput"
        />

        <!-- Hidden image input -->
        <input ref="image_input_ref" type="file" accept="image/*" class="hidden" @change="handleImageUpload" />
    </div>
</template>

<style>
.blog-content:empty::before {
    content: attr(data-placeholder);
    color: var(--muted-foreground);
    pointer-events: none;
}

.blog-content h2 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 1.75rem;
    margin-bottom: 0.75rem;
    color: var(--foreground);
}

.blog-content h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    color: var(--foreground);
}

.blog-content p {
    margin-bottom: 1rem;
    line-height: 1.75;
}

.blog-content strong {
    font-weight: 700;
}

.blog-content em {
    font-style: italic;
}

.blog-content u {
    text-decoration: underline;
}

.blog-content s {
    text-decoration: line-through;
}

.blog-blockquote,
.blog-content blockquote {
    border-left: 4px solid var(--primary);
    margin: 1.5rem 0;
    padding: 1rem 1.25rem;
    background: var(--muted);
    border-radius: 0 0.5rem 0.5rem 0;
    font-style: italic;
    color: var(--muted-foreground);
}

.blog-content pre {
    background: var(--muted);
    border: 1px solid var(--border);
    border-radius: 0.5rem;
    padding: 1rem 1.25rem;
    font-family: ui-monospace, monospace;
    font-size: 0.875rem;
    overflow-x: auto;
    margin-bottom: 1rem;
}

.blog-content hr {
    border: none;
    border-top: 1px solid var(--border);
    margin: 1.5rem 0;
}

.blog-content ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}

.blog-content ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}

.blog-content li {
    margin-bottom: 0.25rem;
    line-height: 1.75;
}

.blog-content a {
    color: var(--primary);
    text-decoration: underline;
}

.blog-image,
.blog-content img {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    margin: 1rem 0;
}
</style>
