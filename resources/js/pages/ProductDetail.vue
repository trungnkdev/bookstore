<script setup lang="ts">
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
  import { type BreadcrumbItem } from '@/types';
  import Heading from '@/components/Heading.vue';
  import PublicLayout from '@/layouts/PublicLayout.vue';
  import { Button } from '@/components/ui/button';
  import ProductRelated from '@/components/ProductRelated.vue';
  import { useCartStore } from '@/stores/cart'

  const breadcrumbItems: BreadcrumbItem[] = [
    {
      title: 'Products',
      href: '/home',
    },
  ];

  export interface Product {
    id: number
    name: string,
    category_id: number,
    price: number,
    image: string,
    description: string,
  }

  const props = defineProps<{
    product: Product,
    relatedProducts: Array<Product>
  }>();

  const cart = useCartStore()
</script>

<template>
  <PublicLayout :breadcrumbs="breadcrumbItems">
    <Head title="Product Detail">
      <link rel="preconnect" href="https://rsms.me/" />
      <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="px-4 py-6">
      <div class="grid gap-5 row-gap-8 lg:col-span-2 md:grid-cols-2">
        <div class="border-[1px] rounded-lg">
          <img
            :src="`/storage/${product.image}`"
            :alt="`Image of ${product.name}`"
            class="w-full h-full object-cover rounded-lg mb-3"
          />
        </div>
        <div class="p-4">
          <h2 class="text-2xl">{{ product.name }}</h2>
          <p>{{ product.price }}</p>
          <Button @click="cart.addToCart(product)">Add To Cart</Button>
          <div class="divide-y-4"></div>
          <p>Share:</p>
          <p>Tags:</p>
        </div>
      </div>
      <div class="mt-4">
        <div class="border-b-2">
          <Button variant="ghost" class="border-t-2 border-l-2 border-r-2 py-6 bg-white translate-y-[2px]">Description</Button>
        </div>
        <div class="border-b-2 border-l-2 border-r-2">
          <p class="px-4 py-8">{{ product.description }}</p>
        </div>
      </div>
      <div class="mt-8">
        <Heading title="Related Products" class="mb-4" />
        <ProductRelated :products="relatedProducts" />
      </div>
    </div>
  </PublicLayout>
</template>