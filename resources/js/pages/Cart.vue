<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
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
  NumberField,
  NumberFieldContent,
  NumberFieldDecrement,
  NumberFieldIncrement,
  NumberFieldInput,
} from '@/components/ui/number-field'
import { ShoppingCart } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { useCartStore } from '@/stores/cart'
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Trash } from 'lucide-vue-next';

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
    <Head title="Cart">
      <link rel="preconnect" href="https://rsms.me/" />
      <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="px-4 py-6">
      <div class="flex justify-between mb-4">
        <Heading title="Cart" description="" class="mb-0" />
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4">
        <div class="w-full px-4 col-span-2">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead class="w-[100px]">
                  Item
                </TableHead>
                <TableHead></TableHead>
                <TableHead>Quantity</TableHead>
                <TableHead>Price</TableHead>
                <TableHead class="text-right">
                  Total
                </TableHead>
                <TableHead>

                </TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="item in cart.items" :key="item.id">
                <TableCell class="font-medium">
                  <img :src="`/storage/${item.image}`" alt="">
                </TableCell>
                <TableCell class="font-medium">
                  <p>{{ item.name }}</p>
                  <p>{{ item.description }}</p>
                </TableCell>
                <TableCell>
                  <NumberField class="w-[100px]" :default-value="1" :min="1">
                    <NumberFieldContent>
                      <NumberFieldDecrement />
                      <NumberFieldInput />
                      <NumberFieldIncrement />
                    </NumberFieldContent>
                  </NumberField>
                </TableCell>
                <TableCell>{{ item.price }}</TableCell>
                <TableCell class="text-right">
                  {{ item.totalAmount }}
                </TableCell>
                <TableCell class="text-right">
                  <Trash/>
                </TableCell>
                
              </TableRow>
            </TableBody>
          </Table>
        </div>
        <div>1</div>
      </div>
    </div>
  </PublicLayout>
</template>