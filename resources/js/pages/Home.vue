<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import Heading from '@/components/Heading.vue';
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { ShoppingCart } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { useCartStore } from '@/stores/cart'

import PublicLayout from '@/layouts/PublicLayout.vue';

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
  products: Array<Product>
}>();

const cart = useCartStore()

</script>

<template>
  <PublicLayout :breadcrumbs="breadcrumbItems">
    <Head title="Home">
      <link rel="preconnect" href="https://rsms.me/" />
      <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="px-4 py-6">
      <!-- <div class="flex justify-between mb-4">
        <Heading title="Products" description="" class="mb-0" />
        
      </div> -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <Card v-for="product in props.products" :key="product.id">
          <CardContent class="px-4">
            <Link :href="`/products/${product.id}`">
              <img
                :src="`/storage/${product.image}`"
                :alt="`Image of ${product.name}`"
                class="w-full h-48 object-cover rounded-lg mb-3"
              />
            </Link>
            <div class="flex justify-between px-6 pb-6">
              <div>
                <Link :href="`/products/${product.id}`">
                  <h3 class="text-lg font-semibold truncate">{{ product.name }}</h3>
                </Link>
                <p class="text-primary font-bold">{{product.price.toLocaleString()}}</p>
              </div>
              <Button size="icon" @click="cart.addToCart(product)">
                <ShoppingCart class="w-4 h-4" />
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </PublicLayout>
</template>