<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

interface BreadcrumbItem {
  title: string
  href: string
}

defineProps<{
  title: string
  breadcrumbs?: BreadcrumbItem[]
}>()
</script>

<template>
  <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ title }}</h2>
    <nav v-if="breadcrumbs?.length" aria-label="Breadcrumb">
      <ol class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
        <li>
          <Link href="/dashboard" class="hover:text-gray-700 dark:hover:text-gray-300">Home</Link>
        </li>
        <template v-for="(crumb, index) in breadcrumbs" :key="index">
          <li>/</li>
          <li>
            <Link
              :href="crumb.href"
              :class="index === breadcrumbs.length - 1 ? 'text-gray-800 dark:text-white/90' : 'hover:text-gray-700 dark:hover:text-gray-300'"
            >
              {{ crumb.title }}
            </Link>
          </li>
        </template>
      </ol>
    </nav>
  </div>
</template>
