<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, Calendar, ChevronRight, Hash, User } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { Blog, PublicUser, Topic } from '@/types';

const props = defineProps<{
    profile_user: PublicUser;
    topics: (Topic & { blogs: Blog[] })[];
    recent_blogs: Blog[];
}>();

function formatDate(date: string | null) {
    if (!date) return '';
    return new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).format(new Date(date));
}

const initials = props.profile_user.name
    .split(' ')
    .map((w) => w[0])
    .join('')
    .slice(0, 2)
    .toUpperCase();
</script>

<template>
    <Head :title="`${profile_user.name} – Escribe.io`" />

    <div class="min-h-screen bg-background text-foreground">
        <!-- Navbar -->
        <header class="border-b border-border bg-background/80 backdrop-blur-md fixed top-0 left-0 w-full z-10">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
                <Link href="/" class="text-lg font-bold tracking-tight text-foreground">
                    Escribe<span class="text-primary">.</span>io
                </Link>
                <div class="flex items-center gap-3">
                    <Button as-child variant="ghost" size="sm">
                        <Link href="/login">Sign in</Link>
                    </Button>
                    <Button as-child size="sm">
                        <Link href="/register">Start writing</Link>
                    </Button>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-6 py-12 mt-20">
            <!-- Profile header -->
            <section class="mb-14 flex flex-col items-center gap-5 text-center">
                <!-- Avatar -->
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary text-2xl font-bold text-primary-foreground shadow-md">
                    {{ initials }}
                </div>

                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-foreground">
                        {{ profile_user.name }}
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">@{{ profile_user.username }}</p>
                </div>

                <p v-if="profile_user.bio" class="max-w-lg text-base text-muted-foreground">
                    {{ profile_user.bio }}
                </p>

                <div class="flex items-center gap-2 rounded-full border border-border bg-muted/50 px-4 py-1.5">
                    <BookOpen :size="14" class="text-muted-foreground" />
                    <span class="text-sm font-medium text-foreground">{{ profile_user.blogs_count }}</span>
                    <span class="text-sm text-muted-foreground">blog{{ (profile_user.blogs_count ?? 0) !== 1 ? 's' : '' }} published</span>
                </div>
            </section>

            <!-- Topics with blogs -->
            <section v-if="topics.length" class="mb-14">
                <h2 class="mb-6 flex items-center gap-2 text-xl font-semibold text-foreground">
                    <Hash :size="18" />
                    Browse by Topic
                </h2>

                <div class="flex flex-col gap-10">
                    <div v-for="topic in topics" :key="topic.id">
                        <div class="mb-4 flex items-center justify-between">
                            <Badge variant="secondary" class="px-3 py-1 text-sm">
                                {{ topic.name }}
                            </Badge>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <Link
                                v-for="blog in topic.blogs"
                                :key="blog.id"
                                :href="`/u/${profile_user.username}/${blog.slug}`"
                                class="group flex flex-col gap-3 overflow-hidden rounded-xl border border-border bg-card shadow-sm transition-shadow hover:shadow-md"
                            >
                                <div class="h-36 overflow-hidden bg-muted">
                                    <img
                                        v-if="blog.cover_image_url"
                                        :src="blog.cover_image_url"
                                        :alt="blog.title"
                                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    />
                                    <div v-else class="flex h-full w-full items-center justify-center">
                                        <BookOpen :size="28" class="text-muted-foreground/30" />
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 px-4 pb-4">
                                    <h3 class="line-clamp-2 text-sm font-semibold leading-snug text-foreground transition-colors group-hover:text-primary">
                                        {{ blog.title }}
                                    </h3>
                                    <p v-if="blog.excerpt" class="line-clamp-2 text-xs text-muted-foreground">
                                        {{ blog.excerpt }}
                                    </p>
                                    <div class="flex items-center gap-1 text-xs text-muted-foreground">
                                        <Calendar :size="11" />
                                        {{ formatDate(blog.published_at) }}
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recent posts -->
            <section v-if="recent_blogs.length && !topics.length">
                <h2 class="mb-6 flex items-center gap-2 text-xl font-semibold text-foreground">
                    <BookOpen :size="18" />
                    Latest Posts
                </h2>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="blog in recent_blogs"
                        :key="blog.id"
                        :href="`/u/${profile_user.username}/${blog.slug}`"
                        class="group flex flex-col gap-3 overflow-hidden rounded-xl border border-border bg-card shadow-sm transition-shadow hover:shadow-md"
                    >
                        <div class="h-36 overflow-hidden bg-muted">
                            <img
                                v-if="blog.cover_image_url"
                                :src="blog.cover_image_url"
                                :alt="blog.title"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                            <div v-else class="flex h-full w-full items-center justify-center">
                                <BookOpen :size="28" class="text-muted-foreground/30" />
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 px-4 pb-4">
                            <div v-if="blog.topics?.length" class="flex flex-wrap gap-1">
                                <Badge v-for="t in blog.topics" :key="t.id" variant="secondary" class="text-xs">
                                    {{ t.name }}
                                </Badge>
                            </div>
                            <h3 class="line-clamp-2 text-sm font-semibold leading-snug text-foreground transition-colors group-hover:text-primary">
                                {{ blog.title }}
                            </h3>
                            <div class="flex items-center gap-1 text-xs text-muted-foreground">
                                <Calendar :size="11" />
                                {{ formatDate(blog.published_at) }}
                            </div>
                        </div>
                    </Link>
                </div>
            </section>

            <!-- Empty state -->
            <section
                v-if="!topics.length && !recent_blogs.length"
                class="flex flex-col items-center gap-4 py-20 text-center"
            >
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-muted">
                    <User :size="28" class="text-muted-foreground" />
                </div>
                <div>
                    <p class="text-lg font-semibold text-foreground">No posts yet</p>
                    <p class="mt-1 text-sm text-muted-foreground">
                        {{ profile_user.name }} hasn't published any blogs yet. Check back soon!
                    </p>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="mt-16 border-t border-border py-8 text-center text-sm text-muted-foreground">
            <p>
                Powered by
                <Link href="/" class="font-medium text-foreground hover:underline">Escribe.io</Link>
                · Write your story
            </p>
        </footer>
    </div>
</template>
