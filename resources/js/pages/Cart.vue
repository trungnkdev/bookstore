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
                <TableHead>
                  Total
                </TableHead>
                <TableHead class="text-right">
                  #
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
                  <NumberField 
                    class="w-[100px]" 
                    :default-value="item.quantity ? item.quantity : 1" 
                    :min="1"
                    @update:model-value="cart.updateQuantity(item.id, $event)"
                  >
                    <NumberFieldContent>
                      <NumberFieldDecrement />
                      <NumberFieldInput />
                      <NumberFieldIncrement />
                    </NumberFieldContent>
                  </NumberField>
                </TableCell>
                <TableCell>
                  <p>{{ item.price }}</p>
                  <p><s>{{ item.price }}</s></p>
                </TableCell>
                <TableCell>
                  <p>{{ item.price * item.quantity }}</p>
                  <p><s>{{ item.price * item.quantity }}</s></p>
                </TableCell>
                <TableCell class="text-right">
                  <Trash class="w-4" @click="cart.removeFromCart(item.id)"/>
                </TableCell>
                
              </TableRow>
            </TableBody>
          </Table>
        </div>
        <div>
          
        </div>
      </div>
    </div>
  </PublicLayout>
</template>