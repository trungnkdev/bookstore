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

import InputError from '@/components/InputError.vue';

import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Plus, ArrowUpDown, ChevronDown } from 'lucide-vue-next';
import { cn, valueUpdater } from '@/lib/utils'

import DropdownAction from '@/components/DataTableDropDown.vue'
import AppLayout from '@/layouts/AppLayout.vue';
import Password from '../settings/Password.vue'

const breadcrumbItems: BreadcrumbItem[] = [
  {
    title: 'Users',
    href: '/users',
  },
];

export interface User {
  id: number
  name: string,
  email: string,
  password: string,
}

interface Pagination<T> {
  data: T[]
  current_page: number
  last_page: number
  meta: Record<string, any>
  links: Record<string, any>[]
}

const props = defineProps<{
  users: Pagination<User>
  filters: Record<string, any>
}>()

const columns: ColumnDef<User>[] = [
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
    accessorKey: 'email',
    header: ({ column }) => {
      return h(Button, {
        variant: 'ghost',
        onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
      }, () => ['Email', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('name')),
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
const tableData = computed(() => props.users.data)

const table = useVueTable({
  data: tableData,
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
  email: z.string().email(),
  password: z.string().min(6),
  password_confirmation: z.string().min(6),
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
  email: '',
  password: '',
  password_confirmation: '',
})

const search = ref('')

const selectedCategory = ref({
  id: null,
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

function fetchUsers(params = {}) {
  router.get('/users', { ...search, ...params }, { 
    preserveScroll: true,
    preserveState: true 
  })
}


function onSearch() {
  fetchUsers({ search: search.value, page: 1 })
}

// function sort(column) {
//   const direction = props.filters.direction === 'asc' ? 'desc' : 'asc'
//   fetchUsers({ sort: column, direction })
// }

function create() {
  selectedCategory.value = {
    id: null,
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  }
  showDialog.value = true
}

function edit(category: any) {
  selectedCategory.value = { ...category }
  showDialog.value = true
}

function destroy(id: number) {
  deletingCategory.value = id
  showDialogDelete.value = true
}

function confirmDestroy() {
  router.delete(`/users/${deletingCategory.value}`, {
    onSuccess: () => {
      showDialogDelete.value = false
      form.reset()
      fetchUsers()
    },
  })
}

function deleteRows() {
  showDialogBulkDelete.value = true
}

function confirmBulkDelete() {
  const selectedIds = table.getSelectedRowModel().rows.map(row => row.original.id)
  router.delete('/users/bulk-delete', {
    data: { ids: selectedIds },
    preserveScroll: true,
    onSuccess: () => {
      showDialogBulkDelete.value = false
      rowSelection.value = {}
      form.reset()
      fetchUsers()
    },
  })
}

function onSubmit(values: any) {
  form.name = values.name
  form.email = values.email
  form.password = values.password
  form.password_confirmation = values.password_confirmation
  // if (file) {
  //   form.image = file
  // }
  form.post('/users', {
    onSuccess: () => {
      showDialog.value = false
      form.reset()
      fetchUsers()
    },
  })
}

const closeDialog = () => {
  showDialog.value = false
}


function goToPage(page: number) {
  // router.get('/categories', { page }, {
  //   preserveScroll: true,
  //   preserveState: true,
  // })
  fetchUsers({ page })
}
</script>
<template>
  <Head title="Users" />
  <AppLayout :breadcrumbs="breadcrumbItems">
    <div class="px-4 py-6">
      <div class="flex justify-between mb-4">
        <Heading title="Users" description="Manage users" class="mb-0" />
        <Button @click="create">
          <Plus class="mr-2 h-4 w-4" />
          Create
        </Button>
      </div>

      <Card>
        <div class="w-full px-4">
          <div class="flex items-center py-4">
            <Input
              class="max-w-sm"
              placeholder="Filter name..."
              :model-value="table.getColumn('name')?.getFilterValue() as string"
              @update:model-value=" table.getColumn('name')?.setFilterValue($event)"
            />
            <Button
              v-if="table.getFilteredSelectedRowModel().rows.length" 
              @click="deleteRows"
              class="ml-2"
            >
              deleteRows
            </Button>
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <Button variant="outline" class="ml-auto">
                  Columns <ChevronDown class="ml-2 h-4 w-4" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end">
                <DropdownMenuCheckboxItem
                  v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
                  :key="column.id"
                  class="capitalize"
                  :model-value="column.getIsVisible()"
                  @update:model-value="(value) => {
                    column.toggleVisibility(!!value)
                  }"
                >
                  {{ column.id }}
                </DropdownMenuCheckboxItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
          <div class="rounded-md border">
            <DataTable :columns="columns" :data="tableData" />
          </div>
        </div>
      </Card>

      <Form 
        v-slot="{ handleSubmit }" 
        as="" 
        :key="selectedCategory?.id || 'new'" 
        :initial-values="selectedCategory" 
        keep-values 
        :validation-schema="formSchema"
      >
        <Dialog v-model:open="showDialog">
          <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
              <DialogTitle>User</DialogTitle>
              <DialogClose>
                <X />
                <span class="sr-only">Close</span>
              </DialogClose>
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
              <FormField v-slot="{ componentField }" name="email">
                <FormItem>
                  <FormLabel>Email</FormLabel>
                  <FormControl>
                    <Input type="text" v-bind="componentField" />
                  </FormControl>
                  <FormMessage />
                  <InputError :message="form.errors.email" />
                </FormItem>
              </FormField>
              <FormField v-slot="{ componentField }" name="password">
                <FormItem>
                  <FormLabel>Pasword</FormLabel>
                  <FormControl>
                    <Input type="password" v-bind="componentField" />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              </FormField>
              <FormField v-slot="{ componentField }" name="password_confirmation">
                <FormItem>
                  <FormLabel>Confirm password</FormLabel>
                  <FormControl>
                    <Input type="password" v-bind="componentField" />
                  </FormControl>
                  <FormMessage />
                  <InputError :message="form.errors.password" />
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