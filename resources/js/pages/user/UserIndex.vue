<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Pencil, Trash2, Plus, Info, RefreshCw, Search, Key } from 'lucide-vue-next'

import ThemeProvider from '@/layouts/ThemeProvider.vue'
import SidebarProvider from '@/layouts/SidebarProvider.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { DataTable, DataTHead, DataTBody, DataTh, DataTd, DataTr } from '@/components/ui/table'
import Button from '@/components/ui/button/Button.vue'
import Pagination from '@/components/ui/pagination/Pagination.vue'
import PopupModal from '@/components/ui/popup-modal/PopupModal.vue'
import BaseBreadcrumb from '@/components/common/BaseBreadcrumb.vue'
import { formatDate } from '@/lib/utils'

defineOptions({
  layout: [ThemeProvider, SidebarProvider, AdminLayout],
})

interface RoleOption {
  value: string
  label: string
}

interface UserRow {
  id: number
  full_name: string
  email: string
  department_code: string | null
  role: string | null
  last_active_at: string | null
  created_at: string
}

const props = defineProps<{
  users?: { data: UserRow[]; meta: any }
  user_name?: string
  role?: string
  status?: string
  email?: string
  default_password?: string
  roles?: RoleOption[]
}>()

const breadcrumbs = [{ title: 'Users', href: '/users' }]

// Search
const form = useForm({
  user_name: props.user_name ?? '',
  role: props.role ?? '',
})

const onSearch = () => {
  router.get('/users', form.data(), { preserveState: true })
}

const resetSearch = () => {
  form.reset()
  onSearch()
}

// Delete
const showDeleteModal = ref(false)
const showResetModal = ref(false)
const selectedId = ref<number | null>(null)

const setToDelete = (id: number) => {
  selectedId.value = id
  showDeleteModal.value = true
}

const onDelete = () => {
  if (!selectedId.value) return

  router.delete(`/users/${selectedId.value}`, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteModal.value = false
      selectedId.value = null
    },
  })
}

// Reset password
const setToReset = (id: number) => {
  selectedId.value = id
  showResetModal.value = true
}

const downloadCredentials = (email: string, password: string) => {
  const blob = new Blob([`Email: ${email}\nPassword: ${password}`], {
    type: 'text/plain;charset=utf-8',
  })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `def-pass-${email}.txt`
  a.click()
  URL.revokeObjectURL(url)
}

const onResetPassword = () => {
  if (!selectedId.value) return

  router.post(
    `/users/${selectedId.value}/reset-password`,
    {},
    {
      preserveScroll: true,
      onSuccess: (page: any) => {
        showResetModal.value = false
        selectedId.value = null

        if (page.props.email && page.props.default_password) {
          downloadCredentials(page.props.email, page.props.default_password)
        }
      },
    },
  )
}

// Flash banner (replaces the toast store from the previous boilerplate).
// Captured once here rather than watched reactively — session flash data
// is a one-shot value for this page load, and re-watching props.status
// left it vulnerable to flickering if any other visit to this page
// (search, pagination, prefetch) fired in quick succession.
const flashMessage = ref(props.status ?? '')

if (flashMessage.value) {
  setTimeout(() => (flashMessage.value = ''), 4000)

  if (props.email && props.default_password) {
    downloadCredentials(props.email, props.default_password)
  }
}
</script>

<template>
  <Head title="Users" />

  <BaseBreadcrumb title="Users" :breadcrumbs="breadcrumbs" />

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
        Are you sure you want to remove this user?
      </h3>
    </div>
  </PopupModal>

  <PopupModal
    v-model="showResetModal"
    variant="warning"
    confirm-label="Reset"
    @confirm="onResetPassword"
  >
    <div class="text-center">
      <Info class="mx-auto mb-4 h-12 w-12 text-warning-500" />
      <h3 class="text-lg font-normal text-gray-500 dark:text-gray-400">
        Are you sure you want to reset this user's password? A new password will be downloaded.
      </h3>
    </div>
  </PopupModal>

  <div class="flex h-full flex-1 flex-col gap-4 py-4">
    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
      <form class="w-full" @submit.prevent="onSearch">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:gap-2">
          <div class="w-full md:w-1/4">
            <input
              v-model="form.user_name"
              placeholder="Search by name"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            />
          </div>

          <div class="w-full md:w-1/4">
            <select
              v-model="form.role"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            >
              <option value="">All roles</option>
              <option v-for="option in props.roles" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
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
        <Link href="/users/create" class="w-full md:w-auto">
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
            <DataTh class="w-2/12"><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Department</p></DataTh>
            <DataTh class="w-2/12"><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Role</p></DataTh>
            <DataTh class="w-2/12"><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Last Login</p></DataTh>
            <DataTh class="w-2/12"><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Joined</p></DataTh>
            <DataTh class="w-3/12"><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Actions</p></DataTh>
          </DataTr>
        </DataTHead>
        <DataTBody v-if="props.users?.data.length">
          <DataTr v-for="user in props.users?.data" :key="user.id">
            <DataTd><p class="text-xs text-gray-500 dark:text-gray-400">{{ user.id }}</p></DataTd>
            <DataTd>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.full_name }}</p>
              <p class="text-xs text-gray-400 dark:text-gray-500">{{ user.email }}</p>
            </DataTd>
            <DataTd><p class="text-xs text-gray-500 dark:text-gray-400">{{ user.department_code ?? '—' }}</p></DataTd>
            <DataTd><p class="text-xs text-gray-500 dark:text-gray-400">{{ user.role ?? '—' }}</p></DataTd>
            <DataTd><p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(user.last_active_at) }}</p></DataTd>
            <DataTd><p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(user.created_at) }}</p></DataTd>
            <DataTd class="flex gap-2">
              <Link :href="`/users/${user.id}/edit`">
                <Button variant="outline"><Pencil class="h-4 w-4" /></Button>
              </Link>
              <Button variant="outline" type="button" title="Reset Password" @click="setToReset(user.id)">
                <Key class="h-4 w-4 text-warning-500" />
              </Button>
              <Button variant="destructive" type="button" @click="setToDelete(user.id)">
                <Trash2 class="h-4 w-4" />
              </Button>
            </DataTd>
          </DataTr>
        </DataTBody>
        <DataTBody v-else>
          <DataTr>
            <DataTd :colspan="7">
              <p class="text-center text-xs text-gray-500 dark:text-gray-400">No users found</p>
            </DataTd>
          </DataTr>
        </DataTBody>
      </DataTable>
      <Pagination :pagination="props.users?.meta" />
    </div>
  </div>
</template>
