<template>
  <div class="relative" ref="dropdownRef">
    <!-- User Button -->
    <button
      class="flex items-center text-gray-700 dark:text-gray-400 cursor-pointer"
      @click.prevent="toggleDropdown"
      type="button"
    >
      <span
        class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full text-sm font-semibold text-white ltr:mr-3 rtl:ml-3"
        :style="{ backgroundColor: avatarColor }"
      >
        {{ initials }}
      </span>

      <span class="block font-medium text-theme-sm ltr:mr-1 rtl:ml-1">{{ user?.first_name }}</span>

      <ChevronDownIcon
        class="size-5 transition-transform duration-200 text-gray-500 dark:text-gray-400"
        :class="{ 'rotate-180': dropdownOpen }"
      />
    </button>

    <!-- Dropdown Start -->
    <div
      v-if="dropdownOpen"
      class="absolute ltr:right-0 rtl:left-0 z-50 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark animate-fadeIn"
    >
      <!-- User Info -->
      <div>
        <span class="block font-medium text-gray-700 text-theme-sm dark:text-gray-400">
          {{ fullName }}
        </span>
        <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">
          {{ user?.email }}
        </span>
      </div>

      <!-- Menu Items -->
      <ul class="flex flex-col gap-1 pt-4 pb-3 border-b border-gray-200 dark:border-gray-800">
        <li>
          <Link
            href="/profile"
            @click="closeDropdown"
            class="group flex items-center gap-3 rounded-lg px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
          >
            <UserCircleIcon
              class="fill-gray-500 group-hover:fill-gray-700 dark:fill-gray-400 dark:group-hover:fill-gray-300"
            />
            Profile
          </Link>
        </li>
      </ul>

      <!-- Sign Out -->
      <button
        type="button"
        @click="signOut"
        class="group mt-3 flex w-full items-center justify-center gap-3 rounded-lg border border-gray-200 px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
      >
        Sign out
      </button>
    </div>
    <!-- Dropdown End -->
  </div>
</template>

<script setup lang="ts">
import { UserCircleIcon, ChevronDownIcon } from '@/icons'
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'

interface AuthUser {
  first_name: string
  last_name: string
  email: string
}

const page = usePage<{ auth: { user: AuthUser } }>()
const user = computed(() => page.props.auth?.user)

const fullName = computed(() => `${user.value?.first_name ?? ''} ${user.value?.last_name ?? ''}`.trim())

const initials = computed(() => {
  const first = user.value?.first_name?.charAt(0) ?? ''
  const last = user.value?.last_name?.charAt(0) ?? ''
  return (first + last).toUpperCase() || '?'
})

// Deterministic color per user, picked from the app's own theme palette --
// same idea as Google's initial-letter avatars, but stable across reloads
// since it's derived from the user's name rather than random.
const avatarPalette = ['#465FFF', '#0BA5EC', '#12B76A', '#F79009', '#F04438', '#7A5AF8']

const avatarColor = computed(() => {
  const name = fullName.value || '?'
  let hash = 0
  for (let i = 0; i < name.length; i++) {
    hash = (hash * 31 + name.charCodeAt(i)) % avatarPalette.length
  }
  return avatarPalette[Math.abs(hash) % avatarPalette.length]
})

const dropdownOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

const closeDropdown = () => {
  dropdownOpen.value = false
}

const signOut = () => {
  closeDropdown()
  router.post('/logout')
}

const handleClickOutside = (event: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
