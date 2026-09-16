import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

interface AuthProps {
  auth: {
    permissions?: string[]
  }
  [key: string]: unknown
}

export function usePermissions() {
  const page = usePage<AuthProps>()

  const permissions = computed(() => page.props.auth?.permissions ?? [])

  const can = (permission?: string): boolean => {
    if (!permission) return true
    return permissions.value.includes(permission)
  }

  return { can, permissions }
}
