<script setup lang="ts">
import { ref, computed, watch, nextTick, onBeforeUnmount } from 'vue'
import { ChevronDown, Search, Check } from 'lucide-vue-next'

interface Option {
  value: string
  label: string
}

const props = withDefaults(
  defineProps<{
    modelValue: string
    options: Option[]
    placeholder?: string
    id?: string
    disabled?: boolean
  }>(),
  {
    placeholder: 'Search...',
    disabled: false,
  },
)

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

const isOpen = ref(false)
const query = ref('')
const containerRef = ref<HTMLElement | null>(null)
const inputRef = ref<HTMLInputElement | null>(null)
const highlightedIndex = ref(0)

const selectedOption = computed(() => props.options.find((option) => option.value === props.modelValue) ?? null)

const filteredOptions = computed(() => {
  if (!query.value.trim()) return props.options
  const q = query.value.trim().toLowerCase()
  return props.options.filter((option) => option.label.toLowerCase().includes(q))
})

const openDropdown = () => {
  if (props.disabled) return
  isOpen.value = true
  query.value = ''
  highlightedIndex.value = Math.max(
    props.options.findIndex((option) => option.value === props.modelValue),
    0,
  )
  nextTick(() => inputRef.value?.focus())
}

const closeDropdown = () => {
  isOpen.value = false
  query.value = ''
}

const selectOption = (option: Option) => {
  emit('update:modelValue', option.value)
  closeDropdown()
}

const onClickOutside = (event: MouseEvent) => {
  if (containerRef.value && !containerRef.value.contains(event.target as Node)) {
    closeDropdown()
  }
}

const onKeydown = (event: KeyboardEvent) => {
  if (event.key === 'ArrowDown') {
    event.preventDefault()
    highlightedIndex.value = Math.min(highlightedIndex.value + 1, filteredOptions.value.length - 1)
  } else if (event.key === 'ArrowUp') {
    event.preventDefault()
    highlightedIndex.value = Math.max(highlightedIndex.value - 1, 0)
  } else if (event.key === 'Enter') {
    event.preventDefault()
    const option = filteredOptions.value[highlightedIndex.value]
    if (option) selectOption(option)
  } else if (event.key === 'Escape') {
    event.preventDefault()
    closeDropdown()
  }
}

watch(query, () => {
  highlightedIndex.value = 0
})

watch(isOpen, (open) => {
  if (open) {
    window.addEventListener('mousedown', onClickOutside)
  } else {
    window.removeEventListener('mousedown', onClickOutside)
  }
})

onBeforeUnmount(() => {
  window.removeEventListener('mousedown', onClickOutside)
})
</script>

<template>
  <div ref="containerRef" class="relative">
    <!-- Closed state: looks exactly like a normal select trigger -->
    <button
      v-if="!isOpen"
      type="button"
      :id="id"
      :disabled="disabled"
      class="flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 text-left text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
      @click="openDropdown"
    >
      <span :class="selectedOption ? '' : 'text-gray-400 dark:text-gray-500'">
        {{ selectedOption ? selectedOption.label : placeholder }}
      </span>
      <ChevronDown class="h-4 w-4 shrink-0 text-gray-400" />
    </button>

    <!-- Open state: same footprint, swapped for a live search input -->
    <div v-else class="relative">
      <Search class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
      <input
        ref="inputRef"
        v-model="query"
        type="text"
        :placeholder="selectedOption ? selectedOption.label : placeholder"
        class="h-11 w-full rounded-lg border border-brand-300 bg-transparent pl-10 pr-4 text-sm text-gray-800 outline-hidden ring-3 ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
        @keydown="onKeydown"
      />
    </div>

    <ul
      v-if="isOpen"
      class="absolute z-50 mt-1.5 max-h-60 w-full overflow-auto rounded-lg border border-gray-200 bg-white py-1.5 shadow-lg dark:border-gray-700 dark:bg-gray-900"
    >
      <li v-if="!filteredOptions.length" class="px-4 py-2 text-sm text-gray-400 dark:text-gray-500">No matches found</li>
      <li
        v-for="(option, index) in filteredOptions"
        :key="option.value"
        class="flex cursor-pointer items-center justify-between px-4 py-2 text-sm"
        :class="
          index === highlightedIndex
            ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400'
            : 'text-gray-700 dark:text-gray-300'
        "
        @mousedown.prevent="selectOption(option)"
        @mouseenter="highlightedIndex = index"
      >
        <span>{{ option.label }}</span>
        <Check v-if="option.value === modelValue" class="h-4 w-4 shrink-0 text-brand-500" />
      </li>
    </ul>
  </div>
</template>
