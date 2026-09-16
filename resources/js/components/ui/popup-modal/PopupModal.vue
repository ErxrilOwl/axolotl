<script setup lang="ts">
const props = withDefaults(
  defineProps<{
    modelValue: boolean
    variant?: 'error' | 'warning' | 'info'
    confirmLabel?: string
    cancelLabel?: string
  }>(),
  {
    variant: 'info',
    confirmLabel: 'Confirm',
    cancelLabel: 'Cancel',
  },
)

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  confirm: []
}>()

const close = () => emit('update:modelValue', false)
const confirm = () => emit('confirm')

const confirmButtonClasses: Record<string, string> = {
  error: 'bg-error-500 hover:bg-error-600',
  warning: 'bg-warning-500 hover:bg-warning-600',
  info: 'bg-brand-500 hover:bg-brand-600',
}
</script>

<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-99999 flex items-center justify-center bg-gray-900/50 p-4"
    @click.self="close"
  >
    <div
      class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-900"
      role="dialog"
      aria-modal="true"
    >
      <slot />

      <div class="mt-6 flex items-center justify-center gap-3">
        <button
          type="button"
          class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5"
          @click="close"
        >
          {{ cancelLabel }}
        </button>
        <button
          type="button"
          :class="[
            'flex-1 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition-colors',
            confirmButtonClasses[props.variant],
          ]"
          @click="confirm"
        >
          {{ confirmLabel }}
        </button>
      </div>
    </div>
  </div>
</template>
