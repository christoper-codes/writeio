<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { BookOpen, Calendar, Edit, Eye, EyeOff, MoreVertical, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { Blog } from '@/types';

const props = defineProps<{
    blog: Blog;
}>();

const confirm_delete = ref(false);

function togglePublish() {
    router.patch(`/blogs/${props.blog.id}/toggle-publish`, {}, { preserveScroll: true });
}

function deleteBlog() {
    if (!confirm_delete.value) {
        confirm_delete.value = true;
        setTimeout(() => (confirm_delete.value = false), 3000);
        return;
    }
    router.delete(`/blogs/${props.blog.id}`, { preserveScroll: true });
}

function formatDate(date: string | null) {
    if (!date) return '';
    return new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).format(new Date(date));
}
</script>

<template>
    <Card class="group overflow-hidden py-0 transition-shadow hover:shadow-md">
        <!-- Cover image -->
        <div class="relative h-44 w-full overflow-hidden bg-muted">
            <img
                v-if="blog.cover_image_url"
                :src="blog.cover_image_url"
                :alt="blog.title"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
            />
            <div v-else class="flex h-full w-full items-center justify-center">
                <BookOpen :size="36" class="text-muted-foreground/40" />
            </div>

            <!-- Status badge -->
            <div class="absolute left-3 top-3">
                <Badge :variant="blog.status === 'published' ? 'default' : 'secondary'" class="text-xs font-medium">
                    {{ blog.status === 'published' ? 'Published' : 'Draft' }}
                </Badge>
            </div>

            <!-- Menu -->
            <div class="absolute right-2 top-2">
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-7 w-7 rounded-full bg-background/80 backdrop-blur-sm hover:bg-background"
                        >
                            <MoreVertical :size="14" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem as-child>
                            <Link :href="`/blogs/${blog.id}/edit`" class="flex items-center gap-2">
                                <Edit :size="14" />
                                Edit
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem class="flex items-center gap-2 cursor-pointer" @click="togglePublish">
                            <Eye v-if="blog.status === 'draft'" :size="14" />
                            <EyeOff v-else :size="14" />
                            {{ blog.status === 'draft' ? 'Publish' : 'Unpublish' }}
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem
                            class="flex items-center gap-2 cursor-pointer text-destructive focus:text-destructive"
                            @click="deleteBlog"
                        >
                            <Trash2 :size="14" />
                            {{ confirm_delete ? 'Click again to confirm' : 'Delete' }}
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>

        <CardContent class="flex flex-col gap-3 px-4 py-4">
            <!-- Topics -->
            <div v-if="blog.topics?.length" class="flex flex-wrap gap-1.5">
                <Badge
                    v-for="topic in blog.topics"
                    :key="topic.id"
                    variant="secondary"
                    class="text-xs"
                >
                    {{ topic.name }}
                </Badge>
            </div>

            <!-- Title -->
            <Link :href="`/blogs/${blog.id}/edit`">
                <h3 class="line-clamp-2 text-base font-semibold leading-snug text-foreground transition-colors hover:text-primary">
                    {{ blog.title }}
                </h3>
            </Link>

            <!-- Excerpt -->
            <p v-if="blog.excerpt" class="line-clamp-2 text-sm text-muted-foreground">
                {{ blog.excerpt }}
            </p>

            <!-- Date -->
            <div class="flex items-center gap-1.5 text-xs text-muted-foreground">
                <Calendar :size="12" />
                <span v-if="blog.published_at">{{ formatDate(blog.published_at) }}</span>
                <span v-else>{{ formatDate(blog.created_at) }}</span>
            </div>
        </CardContent>
    </Card>
</template>
