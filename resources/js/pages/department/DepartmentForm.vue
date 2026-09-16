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

interface DepartmentPayload {
  id: number
  name: string
  code: string
  fixed: boolean
}

const props = defineProps<{
  department?: DepartmentPayload
  isEdit?: boolean
  status?: string
}>()

const breadcrumbs = [
  { title: 'Departments', href: '/departments' },
  {
    title: props.isEdit ? 'Edit Department' : 'Add Department',
    href: props.isEdit ? `/departments/${props.department?.id}/edit` : '/departments/create',
  },
]

const form = useForm({
  name: props.department?.name ?? '',
  code: props.department?.code ?? '',
})

const flashMessage = ref(props.status ?? '')
if (flashMessage.value) {
  setTimeout(() => (flashMessage.value = ''), 4000)
}

const onSubmit = () => {
  if (props.isEdit && props.department) {
    form.put(`/departments/${props.department.id}`, {
      onSuccess: () => router.get('/departments'),
    })
  } else {
    form.post('/departments', {
      onSuccess: () => form.reset(),
    })
  }
}
</script>

<template>
  <Head :title="props.isEdit ? 'Update Department' : 'Add Department'" />

  <BaseBreadcrumb :title="props.isEdit ? 'Edit Department' : 'Add Department'" :breadcrumbs="breadcrumbs" />

  <div
    v-if="flashMessage"
    class="mb-4 rounded-lg bg-success-50 p-3 text-sm font-medium text-success-600 dark:bg-success-500/15"
  >
    {{ flashMessage }}
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
    <p v-if="props.department?.fixed" class="mb-6 text-sm text-warning-600 dark:text-warning-400">
      This is a protected system department. You can edit its name, but it cannot be deleted.
    </p>
    <p v-else class="mb-6 text-sm text-gray-500 dark:text-gray-400">Fill the required fields (*)</p>

    <form class="space-y-6" @submit.prevent="onSubmit">
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
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
          <label for="code" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Code<span class="text-error-500">*</span>
          </label>
          <input
            v-model="form.code"
            id="code"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          />
          <p v-if="form.errors.code" class="mt-1.5 text-sm text-error-500">{{ form.errors.code }}</p>
        </div>
      </div>

      <div class="flex items-center justify-end">
        <Button type="submit" :disabled="form.processing">Save</Button>
      </div>
    </form>
  </div>
</template>
