<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Hash, Loader2, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, Topic } from '@/types';

defineProps<{
    topics: (Topic & { blogs_count: number })[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Topics', href: '/topics' },
];

const form = useForm({ name: '' });

function submit() {
    form.post('/topics', {
        onSuccess: () => form.reset(),
        preserveScroll: true,
    });
}

const confirm_delete = ref<number | null>(null);

function deleteTopic(id: number) {
    if (confirm_delete.value !== id) {
        confirm_delete.value = id;
        setTimeout(() => (confirm_delete.value = null), 3000);
        return;
    }
    router.delete(`/topics/${id}`, { preserveScroll: true });
    confirm_delete.value = null;
}
</script>

<template>
    <Head title="Topics" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <Heading
                title="Topics"
                description="Organize your blogs by topic. Topics appear on your public profile page."
            />

            <div class="grid gap-6 lg:grid-cols-[1fr_340px]">
                <!-- Topic list -->
                <div class="flex flex-col gap-3">
                    <div v-if="topics.length" class="flex flex-col gap-2">
                        <div
                            v-for="topic in topics"
                            :key="topic.id"
                            class="flex items-center justify-between gap-3 rounded-lg border border-border bg-card px-4 py-3 transition-colors hover:bg-muted/40"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-muted">
                                    <Hash :size="15" class="text-muted-foreground" />
                                </div>
                                <div>
                                    <p class="font-medium text-foreground">{{ topic.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ topic.blogs_count }} blog{{ topic.blogs_count !== 1 ? 's' : '' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <Badge variant="secondary" class="text-xs">
                                    {{ topic.slug }}
                                </Badge>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="h-7 w-7 text-muted-foreground hover:text-destructive"
                                    :title="confirm_delete === topic.id ? 'Click again to confirm' : 'Delete topic'"
                                    @click="deleteTopic(topic.id)"
                                >
                                    <Trash2 :size="14" :class="{ 'text-destructive': confirm_delete === topic.id }" />
                                </Button>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-border py-16 text-center"
                    >
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                            <Hash :size="22" class="text-muted-foreground" />
                        </div>
                        <div>
                            <p class="font-semibold text-foreground">No topics yet</p>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Create your first topic to categorize your blogs.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Create topic form -->
                <div>
                    <Card class="py-5">
                        <CardHeader class="px-5 pb-2 pt-0">
                            <CardTitle class="text-base">New Topic</CardTitle>
                        </CardHeader>
                        <CardContent class="px-5">
                            <form class="flex flex-col gap-3" @submit.prevent="submit">
                                <div class="flex flex-col gap-1.5">
                                    <Label for="topic-name">Name</Label>
                                    <Input
                                        id="topic-name"
                                        v-model="form.name"
                                        placeholder="e.g. Technology, Travel..."
                                        maxlength="60"
                                    />
                                    <InputError :message="form.errors.name" />
                                </div>
                                <Button type="submit" :disabled="form.processing || !form.name">
                                    <Loader2 v-if="form.processing" :size="15" class="mr-1.5 animate-spin" />
                                    <Plus v-else :size="15" class="mr-1.5" />
                                    Add Topic
                                </Button>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
