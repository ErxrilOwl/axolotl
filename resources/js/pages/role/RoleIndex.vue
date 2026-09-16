<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { Pencil, Trash2, Plus, Info, RefreshCw, Search } from 'lucide-vue-next'

import ThemeProvider from '@/layouts/ThemeProvider.vue'
import SidebarProvider from '@/layouts/SidebarProvider.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { DataTable, DataTHead, DataTBody, DataTh, DataTd, DataTr } from '@/components/ui/table'
import Button from '@/components/ui/button/Button.vue'
import Pagination from '@/components/ui/pagination/Pagination.vue'
import PopupModal from '@/components/ui/popup-modal/PopupModal.vue'
import BaseBreadcrumb from '@/components/common/BaseBreadcrumb.vue'

defineOptions({
  layout: [ThemeProvider, SidebarProvider, AdminLayout],
})

interface RoleRow {
  id: number
  name: string
  users_count: number
  permissions_count: number
}

const props = defineProps<{
  roles?: { data: RoleRow[]; meta: any }
  search?: string
  status?: string
}>()

const breadcrumbs = [{ title: 'Roles', href: '/roles' }]

const form = useForm({
  search: props.search ?? '',
})

const onSearch = () => {
  router.get('/roles', form.data(), { preserveState: true })
}

const resetSearch = () => {
  form.reset()
  onSearch()
}

const showDeleteModal = ref(false)
const selectedId = ref<number | null>(null)

const setToDelete = (id: number) => {
  selectedId.value = id
  showDeleteModal.value = true
}

const onDelete = () => {
  if (!selectedId.value) return

  router.delete(`/roles/${selectedId.value}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false
      selectedId.value = null
    },
  })
}

const flashMessage = ref(props.status ?? '')
if (flashMessage.value) {
  setTimeout(() => (flashMessage.value = ''), 4000)
}

// Delete stays on this same mounted instance and updates props.status in
// place without a remount, so a plain (non-immediate) watch is needed to
// catch it without reintroducing the earlier immediate-watch flicker bug.
watch(
  () => props.status,
  (status, previous) => {
    if (status && status !== previous) {
      flashMessage.value = status
      setTimeout(() => (flashMessage.value = ''), 4000)
    }
  },
)
</script>

<template>
  <Head title="Roles" />

  <BaseBreadcrumb title="Roles" :breadcrumbs="breadcrumbs" />

  <div
    v-if="flashMessage"
    class="mb-4 rounded-lg bg-success-50 p-3 text-sm font-medium text-success-600 dark:bg-success-500/15"
  >
    {{ flashMessage }}
  </div>

  <PopupModal v-model="showDeleteModal" variant="error" confirm-label="Delete" @confirm="onDelete">
    <div class="text-center">
      <Info class="mx-auto mb-4 h-12 w-12 text-gray-400 dark:text-gray-200" />
      <h3 class="text-lg font-normal text-gray-500 dark:text-gray-400">
        Are you sure you want to remove this role?
      </h3>
    </div>
  </PopupModal>

  <div class="flex h-full flex-1 flex-col gap-4 py-4">
    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
      <form class="w-full" @submit.prevent="onSearch">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:gap-2">
          <div class="w-full md:w-1/3">
            <input
              v-model="form.search"
              placeholder="Search by name"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            />
          </div>
          <div class="flex flex-col gap-2 md:flex-row md:items-center">
            <Button variant="lightsecondary" type="button" @click="resetSearch">
              <RefreshCw class="h-4 w-4" />
            </Button>
            <Button variant="info" type="submit">
              <Search class="h-4 w-4" />
              <span class="ml-1">Search</span>
            </Button>
          </div>
        </div>
      </form>

      <div class="w-full md:w-auto">
        <Link href="/roles/create" class="w-full md:w-auto">
          <Button class="w-full md:w-auto">
            <Plus class="h-4 w-4" />
            <span class="ml-1">Add</span>
          </Button>
        </Link>
      </div>
    </div>

    <div>
      <DataTable>
        <DataTHead>
          <DataTr>
            <DataTh class="w-1/12"><p class="text-xs font-medium text-gray-500 dark:text-gray-400">ID</p></DataTh>
            <DataTh class="w-3/12"><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Name</p></DataTh>
            <DataTh class="w-2/12"><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Users</p></DataTh>
            <DataTh class="w-2/12"><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Permissions</p></DataTh>
            <DataTh class="w-2/12"><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Actions</p></DataTh>
          </DataTr>
        </DataTHead>
        <DataTBody v-if="props.roles?.data.length">
          <DataTr v-for="role in props.roles?.data" :key="role.id">
            <DataTd><p class="text-xs text-gray-500 dark:text-gray-400">{{ role.id }}</p></DataTd>
            <DataTd><p class="text-xs text-gray-500 dark:text-gray-400">{{ role.name }}</p></DataTd>
            <DataTd><p class="text-xs text-gray-500 dark:text-gray-400">{{ role.users_count }}</p></DataTd>
            <DataTd><p class="text-xs text-gray-500 dark:text-gray-400">{{ role.permissions_count }}</p></DataTd>
            <DataTd class="flex gap-2">
              <Link :href="`/roles/${role.id}/edit`">
                <Button variant="outline"><Pencil class="h-4 w-4" /></Button>
              </Link>
              <Button variant="destructive" type="button" @click="setToDelete(role.id)">
                <Trash2 class="h-4 w-4" />
              </Button>
            </DataTd>
          </DataTr>
        </DataTBody>
        <DataTBody v-else>
          <DataTr>
            <DataTd :colspan="5">
              <p class="text-center text-xs text-gray-500 dark:text-gray-400">No roles found</p>
            </DataTd>
          </DataTr>
        </DataTBody>
      </DataTable>
      <Pagination :pagination="props.roles?.meta" />
    </div>
  </div>
</template>
