<template>
  <div class="border-t border-gray-100 p-4">
    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl mb-3">
      <div class="w-10 h-10 rounded-full bg-[#2563eb]/10 flex items-center justify-center text-sm font-bold text-[#2563eb] shadow-sm">
        {{ getUserInitial() }}
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-gray-900 truncate">{{ authStore.user?.name }}</p>
        <p class="text-sm text-gray-500">{{ getUserRoleLabel() }}</p>
      </div>
    </div>
    <button @click="logout" class="w-full py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
      </svg>
      <span>Logout</span>
    </button>
  </div>
</template>

<script>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'UserProfile',
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()

    const getUserInitial = () => {
      const name = authStore.user?.name || 'U'
      return name.charAt(0).toUpperCase()
    }

    const getUserRoleLabel = () => {
      const role = authStore.user?.role?.name
      const labels = {
        'admin': 'Administrator',
        'branch_manager': 'Branch Manager',
        'store_manager': 'Store Manager'
      }
      return labels[role] || 'User'
    }

    const logout = async () => {
      try {
        await fetch('/api/logout', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${authStore.token}`,
            'Content-Type': 'application/json'
          }
        })
      } catch (err) {
        console.error('Logout error:', err)
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        authStore.setUser(null)
        authStore.setToken(null)
        router.push('/login')
      }
    }

    return {
      authStore,
      getUserInitial,
      getUserRoleLabel,
      logout
    }
  }
}
</script>