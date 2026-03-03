<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, Calendar, Clock } from 'lucide-vue-next';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { Blog, PublicUser } from '@/types';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';

const props = defineProps<{
    profile_user: PublicUser;
    blog: Blog;
    other_blogs: Blog[];
}>();

function formatDate(date: string | null) {
    if (!date) return '';
    return new Intl.DateTimeFormat('en-US', { month: 'long', day: 'numeric', year: 'numeric' }).format(new Date(date));
}

const read_time = computed(() => {
    const words = props.blog.content?.replace(/<[^>]+>/g, '').split(/\s+/).length ?? 0;
    return Math.max(1, Math.ceil(words / 200));
});

const initials = props.profile_user.name
    .split(' ')
    .map((w) => w[0])
    .join('')
    .slice(0, 2)
    .toUpperCase();
</script>

<template>
    <Head :title="`${blog.title} – ${profile_user.name}`" />

    <div class="min-h-screen bg-background text-foreground">
        <!-- Navbar -->
        <header class="sticky top-0 z-10 border-b border-border bg-background/80 backdrop-blur-md">
            <div class="mx-auto flex max-w-3xl items-center justify-between px-6 py-4">
                <Button as-child variant="ghost" size="sm" class="-ml-2">
                    <Link :href="`/u/${profile_user.username}`">
                        <ArrowLeft :size="15" class="mr-1.5" />
                        Back
                    </Link>
                </Button>
                <div class="flex items-center gap-3">
                    <ThemeToggle />
                    <Link href="/">
                        <div class="flex items-center gap-2">
                                <div class="flex items-center justify-center">
                                    <AppLogoIcon class="size-7" />
                                </div>
                                <span class="hidden lg:block text-sm font-medium tracking-[0.15em]">
                                    Write.io
                                </span>
                        </div>
                    </Link>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-3xl px-6 py-10">
            <!-- Cover image -->
            <div v-if="blog.cover_image_url" class="mb-8 overflow-hidden rounded-2xl">
                <img :src="blog.cover_image_url" :alt="blog.title" class="h-72 w-full object-cover sm:h-96" />
            </div>

            <!-- Meta -->
            <div class="mb-8">
                <!-- Topics -->
                <div v-if="blog.topics?.length" class="mb-4 flex flex-wrap gap-2">
                    <Badge v-for="topic in blog.topics" :key="topic.id" variant="secondary">
                        {{ topic.name }}
                    </Badge>
                </div>

                <!-- Title -->
                <h1 class="mb-4 text-3xl font-bold leading-tight tracking-tight text-foreground sm:text-4xl">
                    {{ blog.title }}
                </h1>

                <!-- Excerpt -->
                <p v-if="blog.excerpt" class="mb-6 text-lg text-muted-foreground leading-relaxed">
                    {{ blog.excerpt }}
                </p>

                <!-- Author + date -->
                <div class="flex items-center gap-3 border-t border-border pt-4">
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground">
                        {{ initials }}
                    </div>
                    <div class="flex flex-col">
                        <Link :href="`/u/${profile_user.username}`" class="text-sm font-medium text-foreground hover:underline">
                            {{ profile_user.name }}
                        </Link>
                        <div class="flex items-center gap-3 text-xs text-muted-foreground">
                            <span class="flex items-center gap-1">
                                <Calendar :size="11" />
                                {{ formatDate(blog.published_at) }}
                            </span>
                            <span class="flex items-center gap-1">
                                <Clock :size="11" />
                                {{ read_time }} min read
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blog content -->
            <article
                class="blog-content prose-like mb-14"
                v-html="blog.content"
            />

            <!-- Other blogs by same author -->
            <section v-if="other_blogs.length" class="border-t border-border pt-10">
                <h2 class="mb-5 text-lg font-semibold text-foreground">More from {{ profile_user.name }}</h2>
                <div class="grid gap-4 sm:grid-cols-3">
                    <Link
                        v-for="other in other_blogs"
                        :key="other.id"
                        :href="`/u/${profile_user.username}/${other.slug}`"
                        class="group flex flex-col gap-2 overflow-hidden rounded-xl border border-border bg-card p-4 transition-shadow hover:shadow-md"
                    >
                        <div v-if="other.cover_image_url" class="mb-1 h-24 overflow-hidden rounded-lg">
                            <img :src="other.cover_image_url" :alt="other.title" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" />
                        </div>
                        <BookOpen v-else :size="18" class="text-muted-foreground" />
                        <h3 class="line-clamp-2 text-sm font-medium text-foreground transition-colors group-hover:text-primary">
                            {{ other.title }}
                        </h3>
                        <p class="text-xs text-muted-foreground">{{ formatDate(other.published_at) }}</p>
                    </Link>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="mt-10 border-t border-border py-8 text-center text-sm text-muted-foreground">
            <p>
                Powered by
                <Link href="/" class="font-medium text-foreground hover:underline">Write.io</Link>
                · Write your story
            </p>
        </footer>
    </div>
</template>

<style>
.prose-like h2 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 0.75rem;
    color: var(--foreground);
}

.prose-like h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-top: 1.75rem;
    margin-bottom: 0.5rem;
    color: var(--foreground);
}

.prose-like p {
    margin-bottom: 1.25rem;
    line-height: 1.85;
    font-size: 1.0625rem;
    color: var(--foreground);
}

.prose-like strong {
    font-weight: 700;
}

.prose-like em {
    font-style: italic;
}

.prose-like blockquote {
    border-left: 4px solid var(--primary);
    margin: 1.75rem 0;
    padding: 1rem 1.5rem;
    background: var(--muted);
    border-radius: 0 0.5rem 0.5rem 0;
    font-style: italic;
    font-size: 1.1rem;
    color: var(--muted-foreground);
}

.prose-like pre {
    background: var(--muted);
    border: 1px solid var(--border);
    border-radius: 0.5rem;
    padding: 1rem 1.25rem;
    font-family: ui-monospace, monospace;
    font-size: 0.875rem;
    overflow-x: auto;
    margin-bottom: 1.25rem;
}

.prose-like hr {
    border: none;
    border-top: 1px solid var(--border);
    margin: 2rem 0;
}

.prose-like ul {
    list-style-type: disc;
    padding-left: 1.75rem;
    margin-bottom: 1.25rem;
}

.prose-like ol {
    list-style-type: decimal;
    padding-left: 1.75rem;
    margin-bottom: 1.25rem;
}

.prose-like li {
    margin-bottom: 0.375rem;
    line-height: 1.75;
}

.prose-like a {
    color: var(--primary);
    text-decoration: underline;
}

.prose-like img {
    max-width: 100%;
    height: auto;
    border-radius: 0.75rem;
    margin: 1.5rem 0;
}
</style>
