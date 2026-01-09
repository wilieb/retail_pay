<template>
  <header class="bg-white border-b border-gray-200 px-4 py-4 flex justify-between items-center sticky top-0 z-10">
    <div>
      <h2 class="text-2xl font-bold text-gray-900">{{ getPageTitle() }}</h2>
    </div>
    <div class="flex items-center gap-4">
      <div class="hidden lg:flex relative">
        <svg class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" placeholder="Search..." class="pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb] w-72 text-sm" />
      </div>

      <button class="relative p-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
      </button>

      <div class="flex items-center gap-3 border-l border-gray-200 pl-4">
        <div class="w-10 h-10 rounded-full bg-[#2563eb]/10 flex items-center justify-center text-sm font-bold text-[#2563eb] shadow-sm">
          {{ getUserInitial() }}
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'Header',
  setup() {
    const route = useRoute()
    const authStore = useAuthStore()

    const getPageTitle = () => {
      const titles = {
        'dashboard': 'Dashboard',
        'inventories': 'Inventory',
        'sales': 'Sales',
        'transfers': 'Transfers',
        'products': 'Products',
        'branches': 'Branches',
        'stores': 'Stores',
        'users': 'Staff Management',
        'activity-logs': 'Activity Logs'
      }
      return titles[route.name] || 'Dashboard'
    }

    const getUserInitial = () => {
      const name = authStore.user?.name || 'U'
      return name.charAt(0).toUpperCase()
    }

    return {
      getPageTitle,
      getUserInitial
    }
  }
}
</script>