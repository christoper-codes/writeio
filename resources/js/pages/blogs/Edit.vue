<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ImagePlus, Loader2, PenSquare, X } from 'lucide-vue-next';
import { ref } from 'vue';
import BlogEditor from '@/components/blogs/BlogEditor.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Blog, BreadcrumbItem, Topic } from '@/types';

const props = defineProps<{
    blog: Blog;
    topics: Topic[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'My Blogs', href: '/blogs' },
    { title: props.blog.title, href: `/blogs/${props.blog.id}/edit` },
];

const form = useForm({
    title: props.blog.title,
    excerpt: props.blog.excerpt ?? '',
    content: props.blog.content ?? '',
    status: props.blog.status,
    cover_image: null as File | null,
    topic_ids: props.blog.topics?.map((t) => t.id) ?? [],
});

const cover_preview = ref<string | null>(props.blog.cover_image_url ?? null);

function handleCoverImage(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    form.cover_image = file;
    cover_preview.value = URL.createObjectURL(file);
}

function removeCover() {
    form.cover_image = null;
    cover_preview.value = null;
}

function toggleTopic(id: number) {
    const idx = form.topic_ids.indexOf(id);
    if (idx >= 0) {
        form.topic_ids.splice(idx, 1);
    } else {
        form.topic_ids.push(id);
    }
}

function submit(status: 'draft' | 'published') {
    form.status = status;
    form.post(`/blogs/${props.blog.id}`, {
        forceFormData: true,
        data: { ...form.data(), _method: 'POST' },
    });
}
</script>

<template>
    <Head :title="`Edit – ${blog.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <Heading title="Edit Blog" :description="`Editing: ${blog.title}`" />

            <div class="grid gap-6 lg:grid-cols-[1fr_300px]">
                <!-- Main editor column -->
                <div class="flex flex-col gap-5">
                    <!-- Title -->
                    <div class="flex flex-col gap-1.5">
                        <Label for="title">Title <span class="text-destructive">*</span></Label>
                        <Input
                            id="title"
                            v-model="form.title"
                            placeholder="Your blog title..."
                            class="text-lg font-medium"
                        />
                        <InputError :message="form.errors.title" />
                    </div>

                    <!-- Excerpt -->
                    <div class="flex flex-col gap-1.5">
                        <Label for="excerpt">Excerpt</Label>
                        <Input
                            id="excerpt"
                            v-model="form.excerpt"
                            placeholder="Brief description of your blog post..."
                        />
                        <InputError :message="form.errors.excerpt" />
                        <p class="text-xs text-muted-foreground">
                            Shown in cards and search results. Max 500 characters.
                        </p>
                    </div>

                    <!-- Content editor -->
                    <div class="flex flex-col gap-1.5">
                        <Label>Content</Label>
                        <BlogEditor
                            v-model="form.content"
                            placeholder="Start writing your blog post..."
                        />
                        <InputError :message="form.errors.content" />
                    </div>
                </div>

                <!-- Sidebar column -->
                <div class="flex flex-col gap-4">
                    <!-- Publish actions -->
                    <Card class="py-4">
                        <CardHeader class="px-4 pb-2 pt-0">
                            <CardTitle class="text-sm font-semibold">Publish</CardTitle>
                        </CardHeader>
                        <CardContent class="flex flex-col gap-2 px-4">
                            <Badge
                                :variant="blog.status === 'published' ? 'default' : 'secondary'"
                                class="mb-1 w-fit capitalize"
                            >
                                {{ blog.status }}
                            </Badge>

                            <Button
                                class="w-full"
                                :disabled="form.processing"
                                @click="submit('published')"
                            >
                                <Loader2 v-if="form.processing && form.status === 'published'" :size="15" class="mr-1.5 animate-spin" />
                                <PenSquare v-else :size="15" class="mr-1.5" />
                                {{ blog.status === 'published' ? 'Save changes' : 'Publish' }}
                            </Button>
                            <Button
                                variant="outline"
                                class="w-full"
                                :disabled="form.processing"
                                @click="submit('draft')"
                            >
                                <Loader2 v-if="form.processing && form.status === 'draft'" :size="15" class="mr-1.5 animate-spin" />
                                Save as Draft
                            </Button>
                        </CardContent>
                    </Card>

                    <!-- Cover image -->
                    <Card class="py-4">
                        <CardHeader class="px-4 pb-2 pt-0">
                            <CardTitle class="text-sm font-semibold">Cover Image</CardTitle>
                        </CardHeader>
                        <CardContent class="px-4">
                            <div v-if="cover_preview" class="relative mb-2 overflow-hidden rounded-lg">
                                <img :src="cover_preview" alt="Cover" class="h-36 w-full object-cover" />
                                <button
                                    type="button"
                                    class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-background/80 text-foreground shadow transition-colors hover:bg-background"
                                    @click="removeCover"
                                >
                                    <X :size="12" />
                                </button>
                            </div>

                            <label
                                class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-border py-6 text-center transition-colors hover:border-primary hover:bg-muted/50"
                            >
                                <ImagePlus :size="20" class="text-muted-foreground" />
                                <span class="text-xs text-muted-foreground">
                                    {{ cover_preview ? 'Change image' : 'Upload cover image' }}
                                </span>
                                <input type="file" accept="image/*" class="hidden" @change="handleCoverImage" />
                            </label>
                            <InputError :message="form.errors.cover_image" class="mt-1" />
                        </CardContent>
                    </Card>

                    <!-- Topics -->
                    <Card v-if="topics.length" class="py-4">
                        <CardHeader class="px-4 pb-2 pt-0">
                            <CardTitle class="text-sm font-semibold">Topics</CardTitle>
                        </CardHeader>
                        <CardContent class="flex flex-wrap gap-2 px-4">
                            <Badge
                                v-for="topic in topics"
                                :key="topic.id"
                                :variant="form.topic_ids.includes(topic.id) ? 'default' : 'outline'"
                                class="cursor-pointer select-none transition-colors"
                                @click="toggleTopic(topic.id)"
                            >
                                {{ topic.name }}
                            </Badge>
                            <InputError :message="form.errors.topic_ids" class="w-full" />
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
