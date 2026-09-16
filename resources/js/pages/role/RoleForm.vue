<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

import ThemeProvider from '@/layouts/ThemeProvider.vue'
import SidebarProvider from '@/layouts/SidebarProvider.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import BaseBreadcrumb from '@/components/common/BaseBreadcrumb.vue'

defineOptions({
  layout: [ThemeProvider, SidebarProvider, AdminLayout],
})

interface PermissionOption {
  id: number
  name: string
}

interface RolePayload {
  id: number
  name: string
  permissions?: PermissionOption[]
}

const props = defineProps<{
  role?: RolePayload
  isEdit?: boolean
  permissions?: Record<string, PermissionOption[]>
  status?: string
}>()

const breadcrumbs = [
  { title: 'Roles', href: '/roles' },
  {
    title: props.isEdit ? 'Edit Role' : 'Add Role',
    href: props.isEdit ? `/roles/${props.role?.id}/edit` : '/roles/create',
  },
]

const form = useForm({
  name: props.role?.name ?? '',
  permissions: props.role?.permissions?.map((p) => p.name) ?? [],
})

const flashMessage = ref(props.status ?? '')
if (flashMessage.value) {
  setTimeout(() => (flashMessage.value = ''), 4000)
}

const permissionGroups = props.permissions ?? {}

const toggleGroup = (groupPermissions: PermissionOption[], checked: boolean) => {
  const names = groupPermissions.map((p) => p.name)
  if (checked) {
    form.permissions = [...new Set([...form.permissions, ...names])]
  } else {
    form.permissions = form.permissions.filter((name) => !names.includes(name))
  }
}

const isGroupFullySelected = (groupPermissions: PermissionOption[]) =>
  groupPermissions.every((p) => form.permissions.includes(p.name))

const onSubmit = () => {
  if (props.isEdit && props.role) {
    form.put(`/roles/${props.role.id}`, {
      onSuccess: () => router.get('/roles'),
    })
  } else {
    form.post('/roles', {
      onSuccess: () => form.reset(),
    })
  }
}
</script>

<template>
  <Head :title="props.isEdit ? 'Update Role' : 'Add Role'" />

  <BaseBreadcrumb :title="props.isEdit ? 'Edit Role' : 'Add Role'" :breadcrumbs="breadcrumbs" />

  <div
    v-if="flashMessage"
    class="mb-4 rounded-lg bg-success-50 p-3 text-sm font-medium text-success-600 dark:bg-success-500/15"
  >
    {{ flashMessage }}
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Fill the required fields (*)</p>

    <form class="space-y-6" @submit.prevent="onSubmit">
      <div class="max-w-sm">
        <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
          Name<span class="text-error-500">*</span>
        </label>
        <input
          v-model="form.name"
          id="name"
          class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
        />
        <p v-if="form.errors.name" class="mt-1.5 text-sm text-error-500">{{ form.errors.name }}</p>
      </div>

      <div>
        <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-400">Permissions</label>

        <div v-if="Object.keys(permissionGroups).length === 0" class="text-sm text-gray-500 dark:text-gray-400">
          No permissions have been created yet.
        </div>

        <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="(groupPermissions, groupName) in permissionGroups"
            :key="groupName"
            class="rounded-lg border border-gray-200 p-4 dark:border-gray-800"
          >
            <label class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-800 dark:text-white/90">
              <input
                type="checkbox"
                :checked="isGroupFullySelected(groupPermissions)"
                @change="toggleGroup(groupPermissions, ($event.target as HTMLInputElement).checked)"
                class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20 dark:border-gray-700"
              />
              {{ groupName }}
            </label>
            <div class="space-y-2">
              <label
                v-for="permission in groupPermissions"
                :key="permission.id"
                class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"
              >
                <input
                  type="checkbox"
                  :value="permission.name"
                  v-model="form.permissions"
                  class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20 dark:border-gray-700"
                />
                {{ permission.name }}
              </label>
            </div>
          </div>
        </div>
        <p v-if="form.errors.permissions" class="mt-1.5 text-sm text-error-500">{{ form.errors.permissions }}</p>
      </div>

      <div class="flex items-center justify-end">
        <Button type="submit" :disabled="form.processing">Save</Button>
      </div>
    </form>
  </div>
</template>
