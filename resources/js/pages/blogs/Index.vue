<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, FileText, PenSquare, Rocket } from 'lucide-vue-next';
import BlogCard from '@/components/blogs/BlogCard.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Blog, BlogStats, BreadcrumbItem } from '@/types';

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Props = {
    blogs: {
        data: Blog[];
        current_page: number;
        last_page: number;
        total: number;
        per_page: number;
        links: PaginationLink[];
    };
    stats: BlogStats;
};

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'My Blogs', href: '/blogs' },
];

const stat_cards = [
    { label: 'Total Blogs', key: 'total' as const, icon: BookOpen, color: 'text-primary' },
    { label: 'Published', key: 'published' as const, icon: Rocket, color: 'text-green-500' },
    { label: 'Drafts', key: 'draft' as const, icon: FileText, color: 'text-amber-500' },
];
</script>

<template>
    <Head title="My Blogs" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-start justify-between gap-4">
                <Heading
                    title="My Blogs"
                    description="Manage and publish your personal blog posts."
                />
                <Button as-child>
                    <Link href="/blogs/create">
                        <PenSquare :size="16" class="mr-1.5" />
                        New Blog
                    </Link>
                </Button>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 sm:grid-cols-3">
                <Card
                    v-for="stat in stat_cards"
                    :key="stat.key"
                    class="py-4"
                >
                    <CardContent class="flex items-center gap-4 px-5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                            <component :is="stat.icon" :size="20" :class="stat.color" />
                        </div>
                        <div>
                            <p class="text-2xl font-bold leading-none text-foreground">
                                {{ stats[stat.key] }}
                            </p>
                            <p class="mt-1 text-xs text-muted-foreground">{{ stat.label }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Blog grid -->
            <div v-if="blogs.data.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <BlogCard v-for="blog in blogs.data" :key="blog.id" :blog="blog" />
            </div>

            <!-- Empty state -->
            <div
                v-else
                class="flex flex-1 flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-border py-20 text-center"
            >
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-muted">
                    <PenSquare :size="24" class="text-muted-foreground" />
                </div>
                <div>
                    <p class="font-semibold text-foreground">No blogs yet</p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Write your first blog post and share your ideas with the world.
                    </p>
                </div>
                <Button as-child>
                    <Link href="/blogs/create">Create your first blog</Link>
                </Button>
            </div>

            <!-- Pagination -->
            <div v-if="blogs.last_page > 1" class="flex justify-center gap-2">
                <template v-for="link in blogs.links" :key="link.label">
                    <Button
                        v-if="link.url"
                        as-child
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                    >
                        <Link :href="link.url" v-html="link.label" />
                    </Button>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
