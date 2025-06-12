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
import { ShoppingCart, MoveLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { useCartStore } from '@/stores/cart'
import {
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Trash } from 'lucide-vue-next';

import { toTypedSchema } from '@vee-validate/zod'
import { h } from 'vue'
import * as z from 'zod'

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Checkout',
    href: '/home',
  },
];

const cart = useCartStore()

const form = useForm({
  order_items: []
})

const checkout = () => {
  
}

// Use VeeValidate's useForm for validation
import { useForm as useVeeForm } from 'vee-validate'

const formSchema = toTypedSchema(z.object({
  username: z.string().min(2).max(50),
}))

const { isFieldDirty, handleSubmit } = useVeeForm({
  validationSchema: formSchema,
})

const onSubmit = handleSubmit((values) => {
  console.log('Form submitted with values:', values);
  // Here you can handle the form submission, e.g., send data to the server
  // router.post('/checkout', values);
})

</script>

<template>
  <PublicLayout :breadcrumbs="breadcrumbItems">
    <Head title="Checkout">
      <link rel="preconnect" href="https://rsms.me/" />
      <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="px-4 py-6">
      <div class="flex justify-between mb-4">
        <Heading title="Checkout" description="" class="mb-0" />
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
        <div class="lg:col-span-1">
          <div class="mb-4">Billing address</div>
          <form class="space-y-6" @submit="onSubmit">
            <div class="grid grid-cols-2 gap-4">
              <div class="grid gap-2">
                <FormField v-slot="{ componentField }" name="username" :validate-on-blur="!isFieldDirty">
                  <FormItem>
                    <FormLabel>First name</FormLabel>
                    <FormControl>
                      <Input type="text" placeholder="shadcn" v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>
              </div>
              <div class="grid gap-2">
                <FormField v-slot="{ componentField }" name="username" :validate-on-blur="!isFieldDirty">
                  <FormItem>
                    <FormLabel>Last name</FormLabel>
                    <FormControl>
                      <Input type="text" placeholder="shadcn" v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>
              </div>
            </div>
            <Button type="submit">
              Submit
            </Button>
          </form>
        </div>
        <div class="lg:col-span-1">
          <div>Your cart</div>

        </div>
      </div>
    </div>
  </PublicLayout>
</template>