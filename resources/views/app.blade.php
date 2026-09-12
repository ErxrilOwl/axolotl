<script setup lang="ts">
import { ref } from 'vue'
import Sidebar from '@/components/layout/Sidebar.vue'
import Topbar from '@/components/layout/Topbar.vue'

const sidebarCollapsed = ref(false)
</script>

<template>
  <div class="flex min-h-screen">
    <Sidebar :collapsed="sidebarCollapsed" />

    <div class="flex flex-1 flex-col" :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64'">
      <Topbar @toggle-sidebar="sidebarCollapsed = !sidebarCollapsed" />

      <main class="flex-1 p-4 lg:p-6">
        <slot />
      </main>
    </div>
  </div>
</template>
