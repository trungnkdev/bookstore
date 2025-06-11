<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Tag, LibraryBig, User, ShoppingBasket, Store, BookCopy, Gift, Warehouse, FileChartColumn } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const overviewNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
]
const productNavItems: NavItem[] = [
    {
        title: 'All Products',
        href: '/admin/products',
        icon: LibraryBig,
    },
    {
        title: 'Categories',
        href: '/categories',
        icon: Tag,
    },
    {
        title: 'Tags',
        href: '/tags',
        icon: Tag,
    },
    {
        title: 'Collection',
        href: '/collections',
        icon: BookCopy,
    },
    {
        title: 'Promotions',
        href: '/promotions',
        icon: Gift,
    },
    {
        title: 'Orders',
        href: '/orders',
        icon: ShoppingBasket,
    },
    {
        title: 'Inventory',
        href: '/inventory',
        icon: Warehouse,
    },
    
];

const userNavItems: NavItem[] = [
    {
        title: 'Users',
        href: '/users',
        icon: User,
    },
    {
        title: 'Roles',
        href: '/roles',
        icon: User,
    },
    {
        title: 'Permissions',
        href: '/permissions',
        icon: User,
    },
]

const reportNavItems: NavItem[] = [
    {
        title: 'Revenue',
        href: '/users',
        icon: FileChartColumn,
    },
    {
        title: 'Inventory',
        href: '/roles',
        icon: FileChartColumn,
    },
]

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    // },
    {
        title: 'Store',
        href: '/',
        icon: Store,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="overviewNavItems" groupName="Overview" />
            <NavMain :items="productNavItems" groupName="Product Management" />
            <NavMain :items="reportNavItems" groupName="Report" />
            <NavMain :items="userNavItems" groupName="Access Management" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
