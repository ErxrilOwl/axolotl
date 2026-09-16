<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

import ThemeProvider from '@/layouts/ThemeProvider.vue'
import SidebarProvider from '@/layouts/SidebarProvider.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import Button from '@/components/ui/button/Button.vue'
import BaseBreadcrumb from '@/components/common/BaseBreadcrumb.vue'

defineOptions({
  layout: [ThemeProvider, SidebarProvider, AdminLayout],
})

interface AuthUser {
  first_name: string
  last_name: string
  email: string
}

interface AuthProps {
  auth: { user: AuthUser }
  [key: string]: unknown
}

const props = defineProps<{
  status?: string
}>()

const page = usePage<AuthProps>()
const user = computed(() => page.props.auth?.user)

const breadcrumbs = [{ title: 'Profile', href: '/profile' }]

// One-time capture for a fresh page load, plus a non-immediate watch to
// catch in-place updates from either form below (both stay on this same
// mounted page after saving) — same pattern used across the other index
// pages, see UserIndex.vue for the full reasoning.
const flashMessage = ref(props.status ?? '')
if (flashMessage.value) {
  setTimeout(() => (flashMessage.value = ''), 4000)
}
watch(
  () => props.status,
  (status, previous) => {
    if (status && status !== previous) {
      flashMessage.value = status
      setTimeout(() => (flashMessage.value = ''), 4000)
    }
  },
)

// Profile information
const infoForm = useForm({
  first_name: user.value?.first_name ?? '',
  last_name: user.value?.last_name ?? '',
})

const onSubmitInfo = () => {
  infoForm.put('/profile')
}

// Password
const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const onSubmitPassword = () => {
  passwordForm.put('/profile/password', {
    onSuccess: () => passwordForm.reset(),
    onError: () => passwordForm.reset('current_password', 'password', 'password_confirmation'),
  })
}
</script>

<template>
  <Head title="Profile" />

  <BaseBreadcrumb title="Profile" :breadcrumbs="breadcrumbs" />

  <div
    v-if="flashMessage"
    class="mb-4 rounded-lg bg-success-50 p-3 text-sm font-medium text-success-600 dark:bg-success-500/15"
  >
    {{ flashMessage }}
  </div>

  <div class="flex flex-col gap-6">
    <!-- Profile Information -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
      <h3 class="mb-1 text-lg font-semibold text-gray-800 dark:text-white/90">Profile Information</h3>
      <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Update your first and last name.</p>

      <form class="max-w-md space-y-5" @submit.prevent="onSubmitInfo">
        <div>
          <label for="first_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            First Name<span class="text-error-500">*</span>
          </label>
          <input
            v-model="infoForm.first_name"
            id="first_name"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          />
          <p v-if="infoForm.errors.first_name" class="mt-1.5 text-sm text-error-500">{{ infoForm.errors.first_name }}</p>
        </div>

        <div>
          <label for="last_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Last Name<span class="text-error-500">*</span>
          </label>
          <input
            v-model="infoForm.last_name"
            id="last_name"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          />
          <p v-if="infoForm.errors.last_name" class="mt-1.5 text-sm text-error-500">{{ infoForm.errors.last_name }}</p>
        </div>

        <div class="flex items-center justify-end">
          <Button type="submit" :disabled="infoForm.processing">Save</Button>
        </div>
      </form>
    </div>

    <!-- Password -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
      <h3 class="mb-1 text-lg font-semibold text-gray-800 dark:text-white/90">Update Password</h3>
      <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
        Confirm your current password before setting a new one.
      </p>

      <form class="max-w-md space-y-5" @submit.prevent="onSubmitPassword">
        <div>
          <label for="current_password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Current Password<span class="text-error-500">*</span>
          </label>
          <input
            v-model="passwordForm.current_password"
            id="current_password"
            type="password"
            autocomplete="current-password"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          />
          <p v-if="passwordForm.errors.current_password" class="mt-1.5 text-sm text-error-500">
            {{ passwordForm.errors.current_password }}
          </p>
        </div>

        <div>
          <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            New Password<span class="text-error-500">*</span>
          </label>
          <input
            v-model="passwordForm.password"
            id="password"
            type="password"
            autocomplete="new-password"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          />
          <p v-if="passwordForm.errors.password" class="mt-1.5 text-sm text-error-500">{{ passwordForm.errors.password }}</p>
        </div>

        <div>
          <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Confirm New Password<span class="text-error-500">*</span>
          </label>
          <input
            v-model="passwordForm.password_confirmation"
            id="password_confirmation"
            type="password"
            autocomplete="new-password"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          />
          <p v-if="passwordForm.errors.password_confirmation" class="mt-1.5 text-sm text-error-500">
            {{ passwordForm.errors.password_confirmation }}
          </p>
        </div>

        <div class="flex items-center justify-end">
          <Button type="submit" :disabled="passwordForm.processing">Update Password</Button>
        </div>
      </form>
    </div>
  </div>
</template>
