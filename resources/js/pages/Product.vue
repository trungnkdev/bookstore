<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import Heading from '@/components/Heading.vue';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Categories',
    href: '/categories',
  },
];
</script>

<template>
  <Head title="Product">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>
  <div class="px-4 py-6">
    <div class="flex justify-between mb-4">
      <Heading title="Categories" description="Manage product categories" class="mb-0" />
    </div>
  </div>
</template>