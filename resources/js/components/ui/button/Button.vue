<script setup lang="ts">
withDefaults(
  defineProps<{
    variant?: 'default' | 'outline' | 'destructive' | 'lightsecondary' | 'info'
    type?: 'button' | 'submit' | 'reset'
    disabled?: boolean
  }>(),
  {
    variant: 'default',
    type: 'button',
    disabled: false,
  },
)

const variantClasses: Record<string, string> = {
  default: 'bg-brand-500 text-white hover:bg-brand-600',
  outline:
    'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-white/5',
  destructive: 'bg-error-500 text-white hover:bg-error-600',
  lightsecondary:
    'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10',
  info: 'bg-blue-light-500 text-white hover:bg-blue-light-600',
}
</script>

<template>
  <button
    :type="type"
    :disabled="disabled"
    :class="[
      'inline-flex items-center justify-center gap-1 rounded-lg px-4 py-2.5 text-sm font-medium shadow-theme-xs transition-colors disabled:cursor-not-allowed disabled:opacity-60',
      variantClasses[variant],
    ]"
  >
    <slot />
  </button>
</template>
