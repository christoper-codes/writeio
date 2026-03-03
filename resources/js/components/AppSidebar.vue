<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, ExternalLink, Hash, LayoutGrid, PenSquare } from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';
const page = usePage();
const user = page.props.auth.user as { username?: string } | undefined;

const mainNavItems: NavItem[] = [
    {
        title: 'My Blogs',
        href: '/blogs',
        icon: BookOpen,
    },
    {
        title: 'New Blog',
        href: '/blogs/create',
        icon: PenSquare,
    },
    {
        title: 'Topics',
        href: '/topics',
        icon: Hash,
    },
];

const footerNavItems: NavItem[] = user?.username
    ? [
          {
              title: 'My public page',
              href: `/u/${user.username}`,
              icon: ExternalLink,
          },
      ]
    : [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
