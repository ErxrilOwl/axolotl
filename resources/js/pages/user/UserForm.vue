<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

import ThemeProvider from '@/layouts/ThemeProvider.vue'
import SidebarProvider from '@/layouts/SidebarProvider.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import BaseBreadcrumb from '@/components/common/BaseBreadcrumb.vue'

defineOptions({
  layout: [ThemeProvider, SidebarProvider, AdminLayout],
})

interface Option {
  value?: string
  label?: string
  id?: number
  name?: string
}

interface UserPayload {
  id: number
  first_name: string
  last_name: string
  email: string
  department_id: number | null
  roles?: { id: number; name: string }[]
}

const props = defineProps<{
  user?: UserPayload
  isEdit?: boolean
  roles?: Option[]
  departments?: Option[]
  status?: string
  email?: string
  default_password?: string
}>()

const breadcrumbs = [
  { title: 'Users', href: '/users' },
  { title: props.isEdit ? 'Edit User' : 'Add User', href: props.isEdit ? `/users/${props.user?.id}/edit` : '/users/create' },
]

const form = useForm({
  first_name: props.user?.first_name ?? '',
  last_name: props.user?.last_name ?? '',
  email: props.user?.email ?? '',
  role: props.user?.roles?.length ? props.user.roles[0].name : '',
  department_id: props.user?.department_id ?? '',
  password: '',
})

const flashMessage = ref(props.status ?? '')

const downloadCredentials = (email: string, password: string) => {
  const blob = new Blob([`Email: ${email}\nPassword: ${password}`], {
    type: 'text/plain;charset=utf-8',
  })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `def-pass-${email}.txt`
  document.body.appendChild(a)
  a.click()
  a.remove()
  URL.revokeObjectURL(url)
}

let flashTimeout: ReturnType<typeof setTimeout> | undefined

// Inertia reuses this component instance when the redirect lands back on the
// same page (e.g. store() -> users.create, resetPassword() -> back()), so we
// need to react to prop changes rather than only reading them once on mount.
watch(
  () => props.status,
  (status) => {
    if (!status) return

    flashMessage.value = status
    clearTimeout(flashTimeout)
    flashTimeout = setTimeout(() => (flashMessage.value = ''), 4000)

    if (props.email && props.default_password) {
      downloadCredentials(props.email, props.default_password)
    }
  },
  { immediate: true },
)

const onSubmit = () => {
  if (props.isEdit && props.user) {
    form.put(`/users/${props.user.id}`, {
      onSuccess: () => router.get('/users'),
    })
  } else {
    form.post('/users', {
      preserveScroll: false,
      onSuccess: () => form.reset('first_name', 'last_name', 'email', 'role', 'department_id'),
    })
  }
}
</script>

<template>
  <Head :title="props.isEdit ? 'Update User' : 'Add User'" />

  <BaseBreadcrumb :title="props.isEdit ? 'Edit User' : 'Add User'" :breadcrumbs="breadcrumbs" />

  <div
    v-if="flashMessage"
    class="mb-4 rounded-lg bg-success-50 p-3 text-sm font-medium text-success-600 dark:bg-success-500/15"
  >
    {{ flashMessage }}
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Fill the required fields (*)</p>

    <form class="space-y-6" @submit.prevent="onSubmit">
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
          <label for="first_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            First Name<span class="text-error-500">*</span>
          </label>
          <input
            v-model="form.first_name"
            id="first_name"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          />
          <p v-if="form.errors.first_name" class="mt-1.5 text-sm text-error-500">{{ form.errors.first_name }}</p>
        </div>

        <div>
          <label for="last_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Last Name<span class="text-error-500">*</span>
          </label>
          <input
            v-model="form.last_name"
            id="last_name"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          />
          <p v-if="form.errors.last_name" class="mt-1.5 text-sm text-error-500">{{ form.errors.last_name }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
          <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Email<span class="text-error-500">*</span>
          </label>
          <input
            v-model="form.email"
            id="email"
            type="email"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          />
          <p v-if="form.errors.email" class="mt-1.5 text-sm text-error-500">{{ form.errors.email }}</p>
        </div>

        <div>
          <label for="role" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Role<span class="text-error-500">*</span>
          </label>
          <select
            v-model="form.role"
            id="role"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          >
            <option value="" disabled>Select role</option>
            <option v-for="option in props.roles" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
          <p v-if="form.errors.role" class="mt-1.5 text-sm text-error-500">{{ form.errors.role }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
          <label for="department_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Department<span class="text-error-500">*</span>
          </label>
          <select
            v-model="form.department_id"
            id="department_id"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          >
            <option value="" disabled>Select department</option>
            <option v-for="option in props.departments" :key="option.id" :value="option.id">
              {{ option.name }}
            </option>
          </select>
          <p v-if="form.errors.department_id" class="mt-1.5 text-sm text-error-500">{{ form.errors.department_id }}</p>
        </div>

        <div v-if="isEdit">
          <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Password
          </label>
          <input
            v-model="form.password"
            id="password"
            type="password"
            placeholder="Leave blank to keep current password"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          />
          <p v-if="form.errors.password" class="mt-1.5 text-sm text-error-500">{{ form.errors.password }}</p>
        </div>

        <div v-else class="flex items-end">
          <p class="text-sm text-gray-500 dark:text-gray-400">
            A random password will be generated and shown after saving.
          </p>
        </div>
      </div>

      <div class="flex items-center justify-end">
        <Button type="submit" :disabled="form.processing">Save</Button>
      </div>
    </form>
  </div>
</template>
