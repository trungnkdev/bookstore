<script setup lang="ts">
import { ref } from 'vue'
import AppLogo from '@/components/AppLogo.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import type { BreadcrumbItem, NavItem } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Menu, Search, ShoppingCart, User, Home, Book, Tag } from 'lucide-vue-next';
import { computed } from 'vue';
import { useCartStore } from '@/stores/cart'
import { Input } from '@/components/ui/input'

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = computed(() => page.props.auth);

const isCurrentRoute = computed(() => (url: string) => page.url === url);

const activeItemStyles = computed(
    () => (url: string) => (isCurrentRoute.value(url) ? 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100' : ''),
);

const mainNavItems: NavItem[] = [
    {
        title: 'Home',
        href: '/',
        icon: Home,
    },
    {
        title: 'Products',
        href: '/products',
        icon: Tag,
    },
    {
        title: 'Contact',
        href: '/contact',
        icon: Tag,
    },
];

const cart = useCartStore()

const rightNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];

const showInput = ref(false)
const search = ref('')

function hideInput() {
  showInput.value = false
}

const searchProduct = () => {
  if (search.value.trim() !== '') {
    // Redirect to the search results page with the search query
    // window.location.href = `/search?query=${encodeURIComponent(search.value.trim())}`;

    router.get('/search', {
        ...usePage().props.query,
        keyword: encodeURIComponent(search.value.trim())
    }, {
        preserveState: true,
        replace: true
    })
  }
}
</script>

<template>
    <div>
        <div class="border-b border-sidebar-border/80">
            <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl">
                <!-- Mobile Menu -->
                <div class="lg:hidden">
                    <Sheet>
                        <SheetTrigger :as-child="true">
                            <Button variant="ghost" size="icon" class="mr-2 h-9 w-9">
                                <Menu class="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="w-[300px] p-6">
                            <SheetTitle class="sr-only">Navigation Menu</SheetTitle>
                            <SheetHeader class="flex justify-start text-left">
                                <AppLogoIcon class="size-6 fill-current text-black dark:text-white" />
                            </SheetHeader>
                            <div class="flex h-full flex-1 flex-col justify-between space-y-4 py-6">
                                <nav class="-mx-3 space-y-1">
                                    <Link
                                        v-for="item in mainNavItems"
                                        :key="item.title"
                                        :href="item.href"
                                        class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                                        :class="activeItemStyles(item.href)"
                                    >
                                        <component v-if="item.icon" :is="item.icon" class="h-5 w-5" />
                                        {{ item.title }}
                                    </Link>
                                </nav>
                                <div class="flex flex-col space-y-4">
                                    <Button variant="outline" size="icon">
                                      <Link :href="route('cart')">
                                        <ShoppingCart class="w-4 h-4" />
                                        {{ cart.items.length }}
                                      </Link>
                                    </Button>
                                </div>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <Link :href="route('dashboard')" class="flex items-center gap-x-2">
                    <AppLogo />
                </Link>

                <!-- Desktop Menu -->
                <div class="hidden h-full lg:flex lg:flex-1">
                    <NavigationMenu class="ml-10 flex h-full items-stretch">
                        <NavigationMenuList class="flex h-full items-stretch space-x-2">
                            <NavigationMenuItem v-for="(item, index) in mainNavItems" :key="index" class="relative flex h-full items-center">
                                <Link :href="item.href">
                                    <NavigationMenuLink
                                        :class="[navigationMenuTriggerStyle(), activeItemStyles(item.href), 'h-9 cursor-pointer px-3']"
                                    >
                                        <component v-if="item.icon" :is="item.icon" class="mr-2 h-4 w-4" />
                                        {{ item.title }}
                                    </NavigationMenuLink>
                                </Link>
                                <div
                                    v-if="isCurrentRoute(item.href)"
                                    class="absolute bottom-0 left-0 h-0.5 w-full translate-y-px bg-black dark:bg-white"
                                ></div>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <div class="ml-auto flex items-center space-x-2">
                    <div class="relative flex items-center space-x-1">
                        <!-- <Button variant="ghost" size="icon" class="group h-9 w-9 cursor-pointer">
                            <Search class="size-5 opacity-80 group-hover:opacity-100" />
                        </Button> -->
                        <div class="relative">
                            <!-- Nếu đang hiển thị input -->
                            <!-- <Input
                            v-if="showInput"
                            v-model="search"
                            placeholder="Search..."
                            class="w-48 h-9 pr-10"
                            @blur="hideInput"
                            autofocus
                            /> -->

                            <div v-if="showInput" class="relative w-full max-w-sm items-center">
                                <Input id="search" type="text" placeholder="Search..." class="pl-10" v-model="search" @blur="hideInput" @keyup.enter="searchProduct"/>
                                <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                                <Search class="size-5 text-muted-foreground" />
                                </span>
                            </div>

                            <!-- Nếu chưa hiển thị input thì hiển thị nút search -->
                            <Button
                            v-else
                            variant="ghost"
                            size="icon"
                            class="group h-9 w-9 cursor-pointer"
                            @click="showInput = true"
                            >
                            <Search class="size-5 opacity-80 group-hover:opacity-100" />
                            </Button>
                        </div>

                        <div class="hidden space-x-1 lg:flex">
                            <!-- <Button variant="outline" size="icon">
                              <Link :href="route('cart')">
                                <ShoppingCart class="w-4 h-4" />
                                {{ cart.items.length }}
                              </Link>
                            </Button> -->
                            <div class="relative inline-block">
                              <Button variant="ghost" size="icon">
                                <Link :href="route('cart')">
                                  <ShoppingCart class="size-5 opacity-80 group-hover:opacity-100" />
                                </Link>
                              </Button>
                              <div v-if="cart.totalQuantity > 0" class="absolute -top-1 -right-2 text-xs text-white bg-black rounded-full px-[7px] py-[2px]">
                                <span class="sr-only">Cart Items</span>
                                <Badge>{{ cart.totalQuantity }}</Badge>
                              </div>
                            </div>
                        </div>
                    </div>

                    <DropdownMenu v-if="auth.user">
                        <DropdownMenuTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                            >
                                <Avatar class="size-8 overflow-hidden rounded-full">
                                    <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                                    <AvatarFallback class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                                        {{ getInitials(auth.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <Button
                        v-else
                        variant="ghost"
                        size="icon"
                    >
                      <Link :href="route('login')">
                        <User class="w-5 h-5"/>
                      </Link>
                    </Button>
                </div>
            </div>
        </div>

        <div v-if="props.breadcrumbs.length > 1" class="flex w-full border-b border-sidebar-border/70">
            <div class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
