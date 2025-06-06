<script setup lang="ts">
import { h, ref, computed, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3';
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
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { ShoppingCart, Grid2X2, List } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { useCartStore } from '@/stores/cart'

import PublicLayout from '@/layouts/PublicLayout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Products',
    href: '/home',
  },
];

const viewType = ref('grid');
const selectedSort = ref('')

export interface Product {
  id: number
  name: string,
  category_id: number,
  price: number,
  image: string,
  description: string,
}

export interface Category {
  id: number
  name: string,
}

interface Pagination<T> {
  data: T[]
  current_page: number
  last_page: number
  meta: Record<string, any>
  links: Record<string, any>[]
}

const props = defineProps<{
  products: Pagination<Product>
  filters: Record<string, any>
  categories: Array<Category>
}>()

const cart = useCartStore()

watch(selectedSort, (newValue) => {
  router.get('/products', {
    ...usePage().props.query,
    sort: newValue
  }, {
    preserveState: true,
    replace: true
  })
})

</script>

<template>
  <PublicLayout :breadcrumbs="breadcrumbItems">
    <Head title="Products">
      <link rel="preconnect" href="https://rsms.me/" />
      <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="px-4 py-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="col-span-1 md:col-span-1 lg:col-span-1">
          <Heading title="Select Price Range" class="mb-4" />
          <div>
            <h3>Product Categories</h3>
            <ul v-for="category in props.categories" :key="category.id" class="list-disc mb-2 pl-4">
              <li><Link :href="`/products?category=${category.id}`" class="text-black">
                {{ category.name }}
              </Link></li>
            </ul>
          </div>
        </div>
        <div class="lg:col-span-3">
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-4">
            <div class="lg:col-start-1 lg:col-span-1">
              <div class="flex justify-start items-center">
                <span class="mr-2">Views </span>
                <Button @click="viewType='grid'" variant="outline" class="hover:bg-black hover:text-white mr-2" :class="viewType=='grid' ? 'bg-black text-white' : ''" size="icon">
                  <Grid2X2 class="h-4 w-4" />
                </Button>
                <Button @click="viewType='list'" variant="outline" class="hover:bg-black hover:text-white" :class="viewType=='list' ? 'bg-black text-white' : ''" size="icon">
                  <List class="h-4 w-4" />
                </Button>
              </div>
            </div>
            <div class="lg:col-start-2 lg:col-span-1">
              <div class="flex justify-end items-center">
                <span class="mr-2">Sort By</span>
                <Select v-model="selectedSort">
                  <SelectTrigger class="w-[180px]">
                    <SelectValue placeholder="Select a fruit" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectGroup>
                      <SelectLabel>Fruits</SelectLabel>
                      <SelectItem value="asc">
                        Price: Low to high
                      </SelectItem>
                      <SelectItem value="desc">
                        Price: High to low
                      </SelectItem>
                    </SelectGroup>
                  </SelectContent>
                </Select>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-1 gap-4" :class="viewType === 'list' ? '' : 'sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3'">
            <Card v-for="product in props.products.data" :key="product.id">
              <CardContent class="px-4" :class="viewType === 'list' ? 'flex  flex-row' : ''">
                <Link :href="`/products/${product.id}`"
                  :class="viewType === 'list' ? 'basis-1/3' : ''">
                  <img
                    :src="`/storage/${product.image}`"
                    :alt="`Image of ${product.name}`"
                    class="w-full h-48 object-cover rounded-lg mb-3"
                  />
                </Link>
                <CardHeader :class="viewType === 'list' ? 'basis-2/3' : ''">
                  <CardTitle>{{ product.name }}</CardTitle>
                  <CardDescription>{{ product.price }}</CardDescription>
                  <div v-if="viewType === 'list'">
                    <p>{{ product.description }}</p>
                    <Button @click="cart.addToCart(product)">
                      <ShoppingCart class="mr-2" />
                      Add To Cart
                    </Button>
                  </div>
                </CardHeader>
              </CardContent>
              <CardFooter v-if="viewType === 'grid'">
                <Button @click="cart.addToCart(product)">
                  <ShoppingCart class="mr-2" />
                  Add To Cart
                </Button>
              </CardFooter>
            </Card>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>