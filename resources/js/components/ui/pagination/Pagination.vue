<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

interface PaginationLink {
  url: string | null
  label: string
  active: boolean
}

interface PaginationMeta {
  current_page: number
  last_page: number
  from: number | null
  to: number | null
  total: number
  links: PaginationLink[]
}

defineProps<{
  pagination?: PaginationMeta
}>()
</script>

<template>
  <div
    v-if="pagination && pagination.links.length > 3"
    class="flex flex-col items-center justify-between gap-3 border-t border-gray-200 px-2 py-4 dark:border-gray-800 sm:flex-row"
  >
    <p class="text-sm text-gray-500 dark:text-gray-400">
      Showing {{ pagination.from ?? 0 }} to {{ pagination.to ?? 0 }} of {{ pagination.total }} results
    </p>
    <div class="flex flex-wrap items-center gap-1">
      <template v-for="(link, index) in pagination.links" :key="index">
        <span
          v-if="!link.url"
          class="rounded-lg px-3 py-1.5 text-sm text-gray-300 dark:text-gray-700"
          v-html="link.label"
        />
        <Link
          v-else
          :href="link.url"
          preserve-scroll
          :class="[
            'rounded-lg px-3 py-1.5 text-sm transition-colors',
            link.active
              ? 'bg-brand-500 text-white'
              : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5',
          ]"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>
