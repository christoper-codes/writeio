<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { login, register } from '@/routes';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Escribe — Write without limits" />

    <div class="relative flex min-h-screen flex-col bg-[#060608] h-screen lg:overflow-hidden">

        <!-- Ambient glow -->
        <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
            <div
                class="h-[600px] w-[900px] rounded-full opacity-20 blur-[120px]"
                style="background: radial-gradient(ellipse, #7c6aff 0%, #3b82f6 40%, transparent 70%);"
            />
        </div>

        <!-- Nav -->
        <header class="relative z-10 flex items-center justify-between px-8 py-7 md:px-14">
            <div class="flex items-center gap-2">
                 <div class="flex items-center justify-center">
                    <AppLogoIcon class="size-8" />
                </div>
                <span class="text-sm font-medium tracking-[0.15em] text-white/90">
                    Write.io
                </span>
            </div>


            <nav class="flex items-center gap-2">
                <template v-if="$page.props.auth.user">
                    <Link
                        href="/blogs"
                        class="rounded-none border border-white/20 bg-white/5 px-5 py-2 text-xs font-medium tracking-wide text-white/80 backdrop-blur-sm transition-all duration-200 hover:border-white/40 hover:bg-white/10 hover:text-white"
                    >
                        Mis Blogs →
                    </Link>
                </template>
                <template v-else>
                    <Link
                        :href="login()"
                        class="px-4 py-2 text-xs font-medium tracking-wide text-white/50 transition-colors duration-200 hover:text-white/80"
                    >
                        Iniciar sesión
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="rounded-none border border-white/20 bg-white/5 px-5 py-2 text-xs font-medium tracking-wide text-white/80 backdrop-blur-sm transition-all duration-200 hover:border-white/40 hover:bg-white/10 hover:text-white"
                    >
                        Comenzar →
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Hero -->
        <main class="relative z-10 flex flex-1 flex-col items-center justify-center px-6 pb-24 pt-12 text-center">

            <!-- Badge -->
            <div class="mb-10 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1.5 backdrop-blur-sm">
                <span class="h-1.5 w-1.5 rounded-full bg-blue-400" style="box-shadow: 0 0 6px 2px rgba(59,130,246,0.6);" />
                <span class="text-[11px] font-medium tracking-widest text-white/50 uppercase">
                    Plataforma de publicación personal
                </span>
            </div>

            <!-- Headline -->
            <h1 class="mx-auto max-w-4xl text-[clamp(3rem,7vw,6.5rem)] font-semibold leading-[1.05] tracking-[-0.03em] text-white">
                Esrcibe sin<br />
                <span class="bg-clip-text text-transparent bg-linear-to-r from-white to-blue-500">
                    límites
                </span>
            </h1>

            <!-- Subtitle -->
            <p class="mx-auto mt-8 max-w-lg text-[15px] leading-[1.75] text-white/40">
               Tu espacio personal para crear artículos, organizar ideas por tema y compartirlas con el mundo. Limpio. Enfocado. Tuyo.
            </p>

            <!-- CTAs -->
            <div class="mt-12 flex flex-col items-center gap-3 sm:flex-row">
                <Link
                    v-if="canRegister"
                    href="/blogs"
                    class="group bg-linear-to-r from-blue-400 to-blue-600 relative inline-flex h-12 min-w-[168px] items-center justify-center overflow-hidden rounded-none px-8 text-sm font-medium text-white transition-all duration-300"
                >
                    <span class="relative z-10 tracking-wide">Comenzar a escribir</span>
                </Link>
                <Link
                    :href="login()"
                    class="inline-flex h-12 min-w-[168px] items-center justify-center rounded-none border border-white/10 bg-white/5 px-8 text-sm font-medium tracking-wide text-white/60 backdrop-blur-sm transition-all duration-200 hover:border-white/20 hover:bg-white/10 hover:text-white/90"
                >
                    Iniciar sesión
                </Link>
            </div>

            <!-- Stats / features row -->
            <div class="mt-20 flex flex-wrap items-center justify-center gap-8">
                <div
                    v-for="item in [
                        { label: 'Edicion enriquecida' },
                        { label: 'Tema organizado' },
                        { label: 'Perfil público' },
                        { label: 'Modo claro y oscuro' },
                    ]"
                    :key="item.label"
                    class="flex items-center gap-2"
                >
                    <span class="h-px w-4 bg-white/20" />
                    <span class="text-[11px] font-medium tracking-[0.15em] text-white/25 uppercase">{{ item.label }}</span>
                </div>
            </div>
        </main>

        <!-- Bottom fade -->
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-[#060608] to-transparent" />
    </div>
</template>
