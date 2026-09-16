<template>
  <Head title="Dashboard" />

  <div class="grid grid-cols-12 gap-4 md:gap-6">
    <!-- Welcome card -->
    <div class="col-span-12">
      <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
          Welcome back, {{ user?.first_name }} 👋
        </h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          You're signed in as {{ user?.email }}. This is a starter dashboard page — swap this section out for real widgets, charts, and tables.
        </p>
      </div>
    </div>

    <!-- Stat cards -->
    <div
      v-for="stat in stats"
      :key="stat.label"
      class="col-span-12 sm:col-span-6 xl:col-span-3 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6"
    >
      <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-xl dark:bg-gray-800">
        <component :is="stat.icon" class="text-gray-800 size-6 dark:text-white/90" />
      </div>
      <div class="flex items-end justify-between mt-5">
        <div>
          <span class="text-sm text-gray-500 dark:text-gray-400">{{ stat.label }}</span>
          <h4 class="mt-2 font-bold text-gray-800 text-title-sm dark:text-white/90">{{ stat.value }}</h4>
        </div>
        <span
          :class="stat.trend === 'up' ? 'text-success-600' : 'text-error-600'"
          class="text-sm font-medium"
        >
          {{ stat.change }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import ThemeProvider from '@/layouts/ThemeProvider.vue'
import SidebarProvider from '@/layouts/SidebarProvider.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { GridIcon, PieChartIcon, TableIcon, UserCircleIcon } from '@/icons'

defineOptions({
  layout: [ThemeProvider, SidebarProvider, AdminLayout],
})

interface AuthUser {
  first_name: string
  last_name: string
  email: string
}

const page = usePage<{ auth: { user: AuthUser } }>()
const user = computed(() => page.props.auth?.user)

const stats = [
  { label: 'Customers', value: '3,782', change: '+11.01%', trend: 'up', icon: GridIcon },
  { label: 'Orders', value: '5,359', change: '-9.05%', trend: 'down', icon: TableIcon },
  { label: 'Revenue', value: '$24,780', change: '+4.20%', trend: 'up', icon: PieChartIcon },
  { label: 'Active Users', value: '1,245', change: '+2.15%', trend: 'up', icon: UserCircleIcon },
]
</script>
