<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
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

export interface Product {
  id: number
  name: string,
  category_id: number,
  price: number,
  image: string,
  description: string,
}
const props = defineProps<{
  products: Product[]
}>()

const cart = useCartStore()
</script>

<template>
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
        <CardHeader>
          <CardTitle>{{ product.name }}</CardTitle>
          <CardDescription>{{ product.price }}</CardDescription>
        </CardHeader>
      </CardContent>
      <CardFooter>
        <Button @click="cart.addToCart(product)">
          <ShoppingCart class="mr-2" />
          Add To Cart
        </Button>
      </CardFooter>
    </Card>
  </div>
  <div class="text-center">
    <Button variant="outline" class="mt-4">
      <Link href="/products">View All Products</Link>
    </Button>
  </div>
</template>