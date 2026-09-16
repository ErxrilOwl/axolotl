<template>
  <Link href="/" class="xl:hidden">
    <img width="40" height="40" class="dark:hidden" :src="Logo" alt="Logo" />
    <img width="40" height="40" class="hidden dark:block" :src="Logo" alt="Logo" />
  </Link>
</template>

<script setup lang="ts">
import Logo from '@/assets/logo.svg';
import { Link } from '@inertiajs/vue3'
</script>
