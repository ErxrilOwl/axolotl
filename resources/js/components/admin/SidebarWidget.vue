<template>
  <div
    class="mx-auto mb-10 w-full max-w-60 rounded-2xl bg-gray-50 px-4 py-5 text-center dark:bg-white/[0.03]"
  >
    <!-- Default slot for custom content -->
    <slot>
      <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">
        <slot name="title">#1 Tailwind CSS Dashboard</slot>
      </h3>

      <p class="mb-4 text-gray-500 text-theme-sm dark:text-gray-400">
        <slot name="description">
          Leading Tailwind CSS Admin Template with 400+ UI Component and Pages.
        </slot>
      </p>

      <a
        :href="buttonUrl"
        target="_blank"
        rel="nofollow"
        class="flex items-center justify-center p-3 font-medium text-white rounded-lg bg-brand-500 text-theme-sm hover:bg-brand-600"
      >
        <slot name="button-text">Purchase Plan</slot>
      </a>
    </slot>
  </div>
</template>

<script setup>
defineProps({
  buttonUrl: {
    type: String,
    default: "https://tailadmin.com/pricing"
  }
})
</script>
