<script setup lang="ts">
import { computed } from 'vue'
import FlatPickr from 'vue-flatpickr-component'
// Already imported globally in app.ts — imported again here too so this
// component still works correctly if it's ever reused somewhere that
// doesn't load it globally. Vite dedupes repeat CSS imports, so this is
// a no-op in the current app.
import 'flatpickr/dist/flatpickr.css'
import { Calendar } from 'lucide-vue-next'

const props = withDefaults(
  defineProps<{
    modelValue: string
    placeholder?: string
    id?: string
    disabled?: boolean
    /** ISO "Y-m-d" lower bound, e.g. to stop an end date before a start date. */
    min?: string
    /** ISO "Y-m-d" upper bound. */
    max?: string
  }>(),
  {
    placeholder: 'Select date',
    disabled: false,
  },
)

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>()

// flatpickr can emit an array when range-mode is used; this component is
// always single-date, but the type still allows it — normalize to a
// plain string either way.
const value = computed({
  get: () => props.modelValue,
  set: (val: string | string[]) => emit('update:modelValue', Array.isArray(val) ? (val[0] ?? '') : val),
})

const config = computed(() => ({
  dateFormat: 'Y-m-d',
  disableMobile: true,
  minDate: props.min || undefined,
  maxDate: props.max || undefined,
}))
</script>

<template>
  <div class="relative">
    <FlatPickr
      v-model="value"
      :id="id"
      :config="config"
      :disabled="disabled"
      :placeholder="placeholder"
      class="h-11 w-full rounded-lg border border-gray-300 bg-transparent pl-4 pr-10 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
    />
    <Calendar class="pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
  </div>
</template>

<style>
/* ---------- Light (base) ---------- */
.flatpickr-calendar {
  background: #ffffff;
  color: #344054;
  border-radius: 0.75rem;
  border: 1px solid #e4e7ec;
  box-shadow:
    0 12px 24px -8px rgba(16, 24, 40, 0.12),
    0 4px 8px -4px rgba(16, 24, 40, 0.08);
  font-family: inherit;
}

.flatpickr-calendar.arrowTop:before,
.flatpickr-calendar.arrowTop:after {
  border-bottom-color: #ffffff;
}
.flatpickr-calendar.arrowBottom:before,
.flatpickr-calendar.arrowBottom:after {
  border-top-color: #ffffff;
}

.flatpickr-calendar .flatpickr-months .flatpickr-month,
.flatpickr-calendar .flatpickr-current-month input.cur-year,
.flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months {
  color: #101828;
  fill: #101828;
  background: transparent;
}

.flatpickr-calendar span.flatpickr-weekday {
  color: #667085;
  background: transparent;
}

.flatpickr-calendar .flatpickr-prev-month svg,
.flatpickr-calendar .flatpickr-next-month svg {
  fill: #667085;
}
.flatpickr-calendar .flatpickr-prev-month:hover svg,
.flatpickr-calendar .flatpickr-next-month:hover svg {
  fill: #465fff;
}

.flatpickr-calendar .flatpickr-day {
  color: #344054;
  background: transparent;
  border-color: transparent;
}

.flatpickr-calendar .flatpickr-day.prevMonthDay,
.flatpickr-calendar .flatpickr-day.nextMonthDay,
.flatpickr-calendar .flatpickr-day.flatpickr-disabled,
.flatpickr-calendar .flatpickr-day.flatpickr-disabled:hover {
  color: #98a2b3;
  background: transparent;
}

/* Hover / keyboard focus — includes today, which flatpickr otherwise
   turns dark grey (#959ea9) on hover. */
.flatpickr-calendar .flatpickr-day:hover,
.flatpickr-calendar .flatpickr-day:focus,
.flatpickr-calendar .flatpickr-day.today:hover,
.flatpickr-calendar .flatpickr-day.today:focus,
.flatpickr-calendar .flatpickr-day.prevMonthDay:hover,
.flatpickr-calendar .flatpickr-day.nextMonthDay:hover {
  background: #eef2ff;
  border-color: #eef2ff;
  color: #344054;
}

.flatpickr-calendar .flatpickr-day.today,
.flatpickr-calendar .flatpickr-day.today:hover,
.flatpickr-calendar .flatpickr-day.today:focus {
  border-color: #465fff;
}

/* Selected day */
.flatpickr-calendar .flatpickr-day.selected,
.flatpickr-calendar .flatpickr-day.selected:hover,
.flatpickr-calendar .flatpickr-day.selected:focus,
.flatpickr-calendar .flatpickr-day.selected.today,
.flatpickr-calendar .flatpickr-day.selected.prevMonthDay,
.flatpickr-calendar .flatpickr-day.selected.nextMonthDay {
  background: #465fff;
  border-color: #465fff;
  color: #ffffff;
}

/* ---------- Dark (layered on top) ---------- */
.dark .flatpickr-calendar {
  background: #101828;
  border-color: #1d2939;
  color: #e4e7ec;
  box-shadow:
    0 12px 24px -8px rgba(0, 0, 0, 0.4),
    0 4px 8px -4px rgba(0, 0, 0, 0.3);
}

.dark .flatpickr-calendar.arrowTop:before,
.dark .flatpickr-calendar.arrowTop:after {
  border-bottom-color: #101828;
}
.dark .flatpickr-calendar.arrowBottom:before,
.dark .flatpickr-calendar.arrowBottom:after {
  border-top-color: #101828;
}

.dark .flatpickr-calendar .flatpickr-months .flatpickr-month,
.dark .flatpickr-calendar .flatpickr-current-month input.cur-year,
.dark .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months {
  color: #e4e7ec;
  fill: #e4e7ec;
  background: transparent;
}

.dark .flatpickr-calendar span.flatpickr-weekday {
  color: #98a2b3;
}

.dark .flatpickr-calendar .flatpickr-prev-month svg,
.dark .flatpickr-calendar .flatpickr-next-month svg {
  fill: #e4e7ec;
}

.dark .flatpickr-calendar .flatpickr-day {
  color: #e4e7ec;
}

.dark .flatpickr-calendar .flatpickr-day.prevMonthDay,
.dark .flatpickr-calendar .flatpickr-day.nextMonthDay,
.dark .flatpickr-calendar .flatpickr-day.flatpickr-disabled,
.dark .flatpickr-calendar .flatpickr-day.flatpickr-disabled:hover {
  color: #475467;
}

.dark .flatpickr-calendar .flatpickr-day:hover,
.dark .flatpickr-calendar .flatpickr-day:focus,
.dark .flatpickr-calendar .flatpickr-day.today:hover,
.dark .flatpickr-calendar .flatpickr-day.today:focus,
.dark .flatpickr-calendar .flatpickr-day.prevMonthDay:hover,
.dark .flatpickr-calendar .flatpickr-day.nextMonthDay:hover {
  background: #1d2939;
  border-color: #1d2939;
  color: #e4e7ec;
}

.dark .flatpickr-calendar .flatpickr-day.today,
.dark .flatpickr-calendar .flatpickr-day.today:hover,
.dark .flatpickr-calendar .flatpickr-day.today:focus {
  border-color: #465fff;
}

.dark .flatpickr-calendar .flatpickr-day.selected,
.dark .flatpickr-calendar .flatpickr-day.selected:hover,
.dark .flatpickr-calendar .flatpickr-day.selected:focus,
.dark .flatpickr-calendar .flatpickr-day.selected.today,
.dark .flatpickr-calendar .flatpickr-day.selected.prevMonthDay,
.dark .flatpickr-calendar .flatpickr-day.selected.nextMonthDay {
  background: #465fff;
  border-color: #465fff;
  color: #ffffff;
}
</style>
