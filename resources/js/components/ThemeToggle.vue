<script setup lang="ts">
import { Moon, Sun } from 'lucide-vue-next';
import { useAppearance } from '@/composables/useAppearance';

const { resolvedAppearance, updateAppearance } = useAppearance();

function toggle() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}
</script>

<template>
    <button
        @click="toggle"
        class="flex h-9 w-9 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/60 backdrop-blur-sm transition-all duration-200 hover:border-white/25 hover:bg-white/10 hover:text-white/90"
        :aria-label="resolvedAppearance === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
    >
        <Sun v-if="resolvedAppearance === 'dark'" class="h-4 w-4" />
        <Moon v-else class="h-4 w-4" />
    </button>
</template>
