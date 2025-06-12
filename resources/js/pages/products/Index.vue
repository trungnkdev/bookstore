<script setup lang="ts">
import { h, ref, computed } from 'vue'
import { toTypedSchema } from '@vee-validate/zod'
import * as z from 'zod'
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3'
import type {
  ColumnDef,
  ColumnFiltersState,
  ExpandedState,
  SortingState,
  VisibilityState,
} from '@tanstack/vue-table'
import {
  FlexRender,
  getCoreRowModel,
  getExpandedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox'
import DataTable from '@/components/DataTable.vue'
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form'

import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'

import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Plus, ArrowUpDown, ChevronDown } from 'lucide-vue-next';
import { cn, valueUpdater } from '@/lib/utils'

import DropdownAction from '@/components/DataTableDropDown.vue'
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
  {
    title: 'Categories',
    href: '/categories',
  },
];

export interface Category {
  id: number
  name: string,
}

export interface Tag {
  id: number
  name: string,
}

export interface Product {
  id: number
  name: string,
  category_id: number,
  price: number,
  image: string,
  description: string,
  tags: Tag[],
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
  tags: Array<Tag>
}>()

// const data = ref<Category[]>([])

const columns: ColumnDef<Product>[] = [
  {
    id: 'select',
    header: ({ table }) => h(Checkbox, {
      'modelValue': table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
      'onUpdate:modelValue': value => table.toggleAllPageRowsSelected(!!value),
      'ariaLabel': 'Select all',
    }),
    cell: ({ row }) => h(Checkbox, {
      'modelValue': row.getIsSelected(),
      'onUpdate:modelValue': value => row.toggleSelected(!!value),
      'ariaLabel': 'Select row',
    }),
    enableSorting: false,
    enableHiding: false,
  },
  {
    accessorKey: 'image',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Image', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => {
      const imagePath = row.original.image
      return h('img', {
        src: `/storage/${imagePath}`,
        alt: 'product image',
        class: 'w-16 h-16 object-cover rounded',
      })
    },
  },
  {
    accessorKey: 'name',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Name', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('name')),
  },
  {
    accessorKey: 'category',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Category', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('category')?.name || 'N/A'),
  },
  {
    accessorKey: 'tags',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Tags', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, ((row.getValue('tags') as Tag[]) || []).map((tag: Tag) => tag.name).join(', ')),
  },
  {
    accessorKey: 'price',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Price', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('price')),
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const category = row.original

      return h(DropdownAction, {
        category,
        onExpand: row.toggleExpanded,
        onEdit: () => edit(category),
        onDelete: () => destroy(category.id),
      })
    },
  },
]
const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})
const expanded = ref<ExpandedState>({})
const tableData = computed(() => props.products)

const table = useVueTable({
  data: tableData.value.data,
  columns,
  getCoreRowModel: getCoreRowModel(),
  // getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
  onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
  onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
  onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
  onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
  onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded),
  state: {
    get sorting() { return sorting.value },
    get columnFilters() { return columnFilters.value },
    get columnVisibility() { return columnVisibility.value },
    get rowSelection() { return rowSelection.value },
    get expanded() { return expanded.value },
  },
})

const formSchema = toTypedSchema(z.object({
  name: z.string().min(2).max(50),
  price: z.number(),
  category_id: z.number().nullable(),
  image: z.any().optional(),
  description: z.string().optional(),
  tags: z.array(z.number()).optional(),
}))

const showDialog = ref(false)
const showDialogDelete = ref(false)
const showDialogBulkDelete = ref(false)
const emit = defineEmits(['close'])
const editingCategory = ref({name: '', image: null})
const deletingCategory = ref(0)
const bulkDeletingCategory = ref([])
const selectedFile = ref<File | null>(null)

const form = useForm({
  name: '',
  price: 0,
  category_id: null,
  image: null,
  description: '',
  tags: [],
})

// const componentField = {
//   onInput: (e: any) => form.image = e.target.files[0],
// }

function handleFileChange(e: any) {
  // const target = event.target as HTMLInputElement
  // const file = target.files?.[0]
  // if (file) {
  //   selectedFile.value = file
  // }
  form.image = e.target.files[0]
}

const search = ref('')

const selectedCategory = ref({
  id: null,
  name: '',
  price: 0,
  image: null,
  description: '',
  tags: [],
})

function fetchProducts(params = {}) {
  router.get('/admin/products', { ...search, ...params }, { 
    preserveScroll: true,
    preserveState: true 
  })
}

function fetchCategories(params = {}) {
  router.get('/categories', { ...search, ...params }, { 
    preserveScroll: true,
    preserveState: true 
  })
}

function onSearch() {
  fetchProducts({ search: search.value, page: 1 })
}

// function sort(column) {
//   const direction = props.filters.direction === 'asc' ? 'desc' : 'asc'
//   fetchUsers({ sort: column, direction })
// }

function create() {
  selectedCategory.value = {
    id: null,
    name: '',
    price: 0,
    image: null,
    description: '',
    tags: [],
  }
  showDialog.value = true
}

function edit(category: any) {
  selectedCategory.value = { ...category }
  selectedCategory.value.tags = category.tags.map((tag: Tag) => tag.id)
  showDialog.value = true
}

function destroy(id: number) {
  deletingCategory.value = id
  showDialogDelete.value = true
}

function confirmDestroy() {
  router.delete(`/admin/products/${deletingCategory.value}`, {
    onSuccess: () => {
      showDialogDelete.value = false
      form.reset()
      fetchProducts()
    },
  })
}

function deleteRows() {
  showDialogBulkDelete.value = true
}

function confirmBulkDelete() {
  const selectedIds = table.getSelectedRowModel().rows.map(row => row.original.id)
  router.delete('/admin/products/bulk-delete', {
    data: { ids: selectedIds },
    preserveScroll: true,
    onSuccess: () => {
      showDialogBulkDelete.value = false
      rowSelection.value = {}
      form.reset()
      fetchProducts()
    },
  })
}

function onSubmit(values: any) {
  // console.log(values)
  // return false;
  // const formData = new FormData()
  // formData.append('name', values.name)
  // const file = selectedFile.value
  // if (file) {
  //   formData.append('image', file)
  // }
  form.name = values.name
  form.price = values.price
  form.category_id = values.category_id
  form.description = values.description
  form.tags = values.tags || []

  // console.log('Form data:', form)
  // if (file) {
  //   form.image = file
  // }
  form.post('/admin/products', {
    onSuccess: () => {
      showDialog.value = false
      form.reset()
      fetchProducts()
    },
  })
}

function goToPage(page: number) {
  // router.get('/categories', { page }, {
  //   preserveScroll: true,
  //   preserveState: true,
  // })
  fetchProducts({ page })
}

function removeImage() {
  selectedCategory.value.image = null
  // Optional: mark image as removed in form state
}

// Handle page change
const handlePageChange = (page: number) => {
  fetchProducts(page)
}

</script>
<template>
  <Head title="Products" />
  <AppLayout :breadcrumbs="breadcrumbItems">
    <div class="px-4 py-6">
      <div class="flex justify-between">
        <Heading title="Products" description="Manage product products" class="mb-0" />
        <Button @click="create">
          <Plus class="mr-2 h-4 w-4" />
          Create
        </Button>
      </div>

      <Card class="py-0">
        <DataTable :columns="columns" :data="tableData" @page-change="goToPage" />
      </Card>

      <Form v-slot="{ handleSubmit }" as="" :key="selectedCategory?.id || 'new'" :initial-values="selectedCategory" keep-values :validation-schema="formSchema">
        <Dialog v-model:open="showDialog">
          <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
              <DialogTitle>Category</DialogTitle>
            </DialogHeader>
            
            <form id="dialogForm" class="space-y-6" @submit="handleSubmit($event, onSubmit)">
              <FormField v-slot="{ componentField }" name="name">
                <FormItem>
                  <FormLabel>Name</FormLabel>
                  <FormControl>
                    <Input type="text" v-bind="componentField" />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              </FormField>
              <FormField v-slot="{ componentField }" name="category_id">
                <FormItem>
                  <FormLabel>Category</FormLabel>

                  <Select v-bind="componentField">
                    <FormControl class="w-full">
                      <SelectTrigger>
                        <SelectValue placeholder="Select a category" />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem 
                          v-for="category in categories" 
                          :key="category.id" 
                          :value="category.id"
                        >
                          {{ category.name }}
                        </SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                  <FormMessage />
                </FormItem>
              </FormField>
              <FormField v-slot="{ componentField }" name="tags">
                <FormItem>
                  <FormLabel>Tags</FormLabel>
                  <Select multiple v-bind="componentField">
                    <FormControl class="w-full">
                      <SelectTrigger>
                        <SelectValue placeholder="Select a tags" />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      <SelectGroup>
                        <SelectItem 
                          v-for="tag in tags" 
                          :key="tag.id" 
                          :value="tag.id"
                        >
                          {{ tag.name }}
                        </SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                </FormItem>
              </FormField>
              <FormField v-slot="{ componentField }" name="price">
                <FormItem>
                  <FormLabel>Price</FormLabel>
                  <FormControl>
                    <Input type="number" v-bind="componentField" />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              </FormField>
              <FormField v-slot="{ componentField }" name="image">
                <FormItem>
                  <FormLabel>Picture</FormLabel>

                  <div v-if="selectedCategory.image" class="mb-2">
                    <a
                      :href="`storage/${selectedCategory.image}`"
                      target="_blank"
                      rel="noopener"
                      class="text-blue-600 underline hover:text-blue-800"
                    >
                      View Image
                    </a>
                    <!-- <Button variant="link">
                      View Image
                    </Button> -->
                    <button
                      type="button"
                      @click="removeImage"
                      class="ml-2 text-red-600 hover:underline"
                    >
                      Remove
                    </button>
                  </div>
                  
                  <FormControl v-else>
                    <Input id="picture" type="file" @change="handleFileChange" />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              </FormField>
              <FormField v-slot="{ componentField }" name="description">
                <FormItem>
                  <FormLabel>Description</FormLabel>
                  <FormControl>
                    <Textarea v-bind="componentField" />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              </FormField>
            </form>

            <DialogFooter>
              <Button type="submit" form="dialogForm">
                Save changes
              </Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </Form>

      <AlertDialog v-model:open="showDialogDelete">
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
            <AlertDialogDescription>
              This action cannot be undone. This will permanently delete your account
              and remove your data from our servers.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction @click="confirmDestroy">Continue</AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      <AlertDialog v-model:open="showDialogBulkDelete">
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
            <AlertDialogDescription>
              This action cannot be undone. This will permanently delete your account
              and remove your data from our servers.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction @click="confirmBulkDelete">Continue</AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  </AppLayout>
</template>