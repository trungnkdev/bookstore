<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Categories',
    href: '/categories',
  },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
      <div class="px-4 py-6">
        <Heading title="Categories" description="Manage product categories" />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
          
        </div>
      </div>
    </AppLayout>
</template>