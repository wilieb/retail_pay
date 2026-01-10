<template>
  <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
    <router-link to="/" class="nav-link" :class="{ 'active': route.name === 'dashboard' }">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
      </svg>
      <span>Dashboard</span>
    </router-link>

    <router-link to="/inventories" class="nav-link" :class="{ 'active': route.name === 'inventories' }">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
      </svg>
      <span>Inventory</span>
    </router-link>

    <router-link to="/branch-inventory" class="nav-link" :class="{ 'active': route.name === 'branch-inventory' }">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
      </svg>
      <span>Branch Inventory</span>
    </router-link>

    <router-link v-if="hasRole(['store_manager', 'branch_manager', 'admin'])" to="/sales" class="nav-link" :class="{ 'active': route.name === 'sales' }">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
      <span>Sales</span>
    </router-link>

    <router-link v-if="hasRole(['branch_manager', 'admin'])" to="/transfers" class="nav-link" :class="{ 'active': route.name === 'transfers' }">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
      </svg>
      <span>Transfers</span>
    </router-link>

    <div v-if="hasRole(['admin'])" class="pt-6 pb-3">
      <div class="px-3 mb-2">
        <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Management</p>
      </div>
    </div>

    <router-link v-if="hasRole(['admin'])" to="/products" class="nav-link" :class="{ 'active': route.name === 'products' }">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
      </svg>
      <span>Products</span>
    </router-link>

    <router-link v-if="hasRole(['admin'])" to="/branches" class="nav-link" :class="{ 'active': route.name === 'branches' }">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
      </svg>
      <span>Branches</span>
    </router-link>

    <router-link v-if="hasRole(['admin'])" to="/stores" class="nav-link" :class="{ 'active': route.name === 'stores' }">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
      </svg>
      <span>Stores</span>
    </router-link>

    <router-link v-if="hasRole(['admin'])" to="/users" class="nav-link" :class="{ 'active': route.name === 'users' }">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
      </svg>
      <span>Staff</span>
    </router-link>

    <router-link v-if="hasRole(['admin'])" to="/permissions" class="nav-link" :class="{ 'active': route.name === 'permissions' }">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7.07-7.07a1 1 0 00-1.414-1.414L9.828 15.172a4 4 0 010 5.656l8.828 8.83a1 1 0 101.414-1.414L11.242 19.414a4 4 0 010-5.656l8.828-8.828z"/>
      </svg>
      <span>Permissions</span>
    </router-link>
  </nav>
</template>

<script>
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'Navigation',
  setup() {
    const route = useRoute()
    const authStore = useAuthStore()

    const hasRole = (roles) => {
      if (!Array.isArray(roles)) roles = [roles]
      if (!authStore.user?.role) return false
      return roles.includes(authStore.user.role.name)
    }

    return {
      route,
      hasRole
    }
  }
}
</script>

<style scoped>
.nav-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  margin-bottom: 0.5rem;
  border-radius: 0.35rem;
  color: #6b7280;
  font-weight: 500;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  position: relative;
}

.nav-link:hover {
  background-color: #f9fafb;
  color: #111827;
}

.nav-link.active {
  background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
}

.nav-link.active svg {
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}
</style>