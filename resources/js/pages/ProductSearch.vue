<script setup lang="ts">
  import { ref } from 'vue';
  import { Head, Link } from '@inertiajs/vue3';
  import { useCartStore } from '@/stores/cart'
  import PublicLayout from '@/layouts/PublicLayout.vue';
  import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
  import { Button } from '@/components/ui/button';
  import { ShoppingCart } from 'lucide-vue-next';
  import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
  import { usePage, router } from '@inertiajs/vue3';

  export interface Product {
    id: number
    name: string,
    category_id: number,
    price: number,
    image: string,
    description: string,
  }

  interface Pagination<T> {
    data: T[]
    current_page: number
    last_page: number
    meta: Record<string, any>
    links: Record<string, any>[]
  }
  const props = defineProps<{
    keyword: string,
    products: Pagination<Product>
  }>()

  const cart = useCartStore()
  const loading = ref(false)

  const prevPage = () => {
    if (props.products.current_page > 1) {
      // loading.value = true
      // window.location.href = `/search/${props.keyword}?page=${props.products.current_page - 1}`
      router.get('/search', {
        ...usePage().props.query,
        keyword: encodeURIComponent(props.keyword),
        page: props.products.current_page - 1
      }, {
        preserveState: true,
        replace: true
      })
    }
  }

  const nextPage = () => {
    if (props.products.current_page < props.products.last_page) {
      // loading.value = true
      // window.location.href = `/search/${props.keyword}?page=${props.products.current_page + 1}`
      router.get('/search', {
        ...usePage().props.query,
        keyword: encodeURIComponent(props.keyword),
        page: props.products.current_page + 1
      }, {
        preserveState: true,
        replace: true
      })
    }
  }
</script>
<template>
  <PublicLayout>
    <Head title="Search Results">
      <link rel="preconnect" href="https://rsms.me/" />
      <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="px-4 py-6">
      <div v-if="products.data.length">
        Showing {{products.data.length}} results for "{{keyword}}"
      </div>
      <div v-else>
        There are no products that match "{{keyword}}"
      </div>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 mt-4">
        <Card v-for="product in products.data" :key="product.id">
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
      <div class="flex justify-between items-center mt-6">
        <span class="text-sm text-gray-600">
          Page {{ products.current_page }} of {{ products.last_page }}
        </span>
        <div class="flex space-x-2">
          <Button @click="prevPage" :disabled="products.current_page <= 1">
            <ChevronLeft class="w-4 h-4" />
            Previous
          </Button>
          <Button @click="nextPage" :disabled="products.current_page >= products.last_page">
            Next
            <ChevronRight class="w-4 h-4" />
          </Button>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>