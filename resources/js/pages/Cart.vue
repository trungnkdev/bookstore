<script setup lang="ts">
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
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
  TableFooter
} from '@/components/ui/table'
import { Trash } from 'lucide-vue-next';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Products',
    href: '/home',
  },
];

const cart = useCartStore()

const form = useForm({
  order_items: []
})

const checkout = () => {

  form.order_items = cart.items

  form.post('/orders', {
    onSuccess: () => {
      cart.clearCart()
      alert('Đặt hàng thành công!')
    },
    onError: () => {
      alert('Có lỗi xảy ra!')
    }
  })
}

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
      <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-1 gap-4">
        <div class="w-full px-4 col-span-2">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>
                  #
                </TableHead>
                <TableHead class="w-[100px]">
                  Item
                </TableHead>
                <TableHead></TableHead>
                <TableHead>Quantity</TableHead>
                <TableHead>Price</TableHead>
                <TableHead>
                  Total
                </TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="item in cart.items" :key="item.id">
                <TableCell class="text-right">
                  <Trash class="w-4" @click="cart.removeFromCart(item.id)"/>
                </TableCell>
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
              </TableRow>
            </TableBody>
            <TableFooter>
              <TableRow>
                <TableHead colSpan="5" class="text-right">
                  TOTAL
                </TableHead>
                <TableHead>
                  {{ cart.totalPrice }}
                </TableHead>
              </TableRow>
            </TableFooter>
          </Table>
          <div class="flex justify-between pt-6 pb-6">
            <Button variant="outline">
              Continue shoping
            </Button>
            <Button @click="checkout">Checkout</Button>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>