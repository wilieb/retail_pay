<template>
  <div class="p-6 space-y-6">
    <div>
      <div class="lg:col-span-2 bg-white rounded-lg p-8 text-gray-900 shadow-xs">
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-xl font-semibold mb-2">
              Welcome Back,
              <span class="text-blue-500">{{ " " + getUserRoleLabel() + "!" }}</span>
            </h2>
            <p class="text-gray-500 text-lg">{{ getCurrentDate() }}</p>
          </div>
          <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
            <svg class="w-9 h-9 text-[#2563eb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-lg p-6 shadow-xs hover:shadow-sm transition-shadow">
        <div class="flex items-start justify-between mb-4">
          <div>
             <h3 class="text-sm font-medium text-gray-500 mb-1">Total Products</h3>
              <p class="text-3xl font-bold text-gray-900 mb-1">{{ stats.totalProducts }}</p>
          </div>
          <span class="text-xs font-semibold text-[#10b981] bg-[#10b981]/10 px-2.5 py-1 rounded-full">In Stock</span>
        </div>
      </div>

      <div class="bg-white rounded-lg p-6 shadow-xs hover:shadow-sm transition-shadow">
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Low Stock Items</h3>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ stats.lowStockItems }}</p>
          </div>
          <span class="text-xs font-semibold text-[#f43f5e] bg-[#f43f5e]/10 px-2.5 py-1 rounded-full">Out of Stock</span>
        </div>
      </div>

      <div class="bg-white rounded-lg p-6 shadow-xs hover:shadow-sm transition-shadow">
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Inventory Value</h3>
            <p class="text-3xl font-bold text-gray-900 mb-1">KES {{ formatNumber(stats.inventoryValue) }}</p>
          </div>
          <span class="text-xs font-semibold text-[#10b981] bg-[#10b981]/10 px-2.5 py-1 rounded-full">Value</span>
        </div>
      </div>

      <div class="bg-white rounded-lg p-6 shadow-xs hover:shadow-sm transition-shadow">
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Total Stock Units</h3>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ formatNumber(stats.totalStockUnits) }}</p>
          </div>
          <span class="text-xs font-semibold text-[#7c3aed] bg-[#7c3aed]/10 px-2.5 py-1 rounded-full">Units</span>
        </div>
      </div>

      <div class="bg-white rounded-lg p-6 shadow-xs hover:shadow-sm transition-shadow">
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Total Sales</h3>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ stats.totalSales }}</p>
          </div>
          <span class="text-xs font-semibold text-[#2563eb] bg-[#2563eb]/10 px-2.5 py-1 rounded-full">Transactions</span>
        </div>
      </div>

      <div class="bg-white rounded-lg p-6 shadow-xs hover:shadow-sm transition-shadow">
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Total Revenue</h3>
            <p class="text-3xl font-bold text-gray-900 mb-1">KES {{ formatNumber(stats.totalRevenue) }}</p>
          </div>
          <span class="text-xs font-semibold text-[#10b981] bg-[#10b981]/10 px-2.5 py-1 rounded-full">Revenue</span>
        </div>
      </div>

      <div class="bg-white rounded-lg p-6 shadow-xs hover:shadow-sm transition-shadow">
        <div class="flex items-start justify-between mb-4">
          <div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Pending Transfers</h3>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ stats.pendingTransfers }}</p>
          </div>
          <span class="text-xs font-semibold text-[#fbbf24] bg-[#fbbf24]/10 px-2.5 py-1 rounded-full">Approval</span>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-lg p-8 shadow-xs">
        <div class="flex items-center gap-3 mb-6">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-id">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3l0 -10" /><path d="M7 10a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M15 8l2 0" /><path d="M15 12l2 0" /><path d="M7 16l10 0" />
          </svg>
          <h3 class="text-xl font-bold text-gray-900">Personal Information</h3>
        </div>

        <div class="space-y-4">
          <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
            <div class="w-10 h-10 bg-[#2563eb]/10 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-[#2563eb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-500 mb-1">Full Name</p>
              <p class="text-base font-semibold text-gray-900">{{ authStore.user?.name }}</p>
            </div>
          </div>

          <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
            <div class="w-10 h-10 bg-[#2563eb]/10 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-[#2563eb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-500 mb-1">Email Address</p>
              <p class="text-base font-semibold text-gray-900 truncate">{{ authStore.user?.email }}</p>
            </div>
          </div>

          <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
            <div class="w-10 h-10 bg-[#2563eb]/10 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-[#2563eb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-500 mb-1">Role</p>
              <p class="text-base font-semibold text-gray-900">{{ getUserRoleLabel() }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg p-8 shadow-xs">
        <div class="flex items-center gap-3 mb-6">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-navigation-pin">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.002 11.676l-4.002 -8.676l-7.97 17.275c-.07 .2 -.017 .424 .135 .572c.15 .148 .374 .193 .57 .116l7.265 -2.463" /><path d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879" /><path d="M19 18v.01" />
          </svg>
          <h3 class="text-xl font-bold text-gray-900">Quick Actions</h3>
        </div>

        <div class="space-y-4">
              <router-link 
                to="/products" 
                class="flex items-center gap-4 p-5 bg-gray-50 rounded-xl transition-all group"
          >
            <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-grey-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="font-bold text-gray-900 text-lg mb-1">Manage Products</h4>
              <p class="text-sm text-gray-600">View and manage product catalog</p>
            </div>
            <svg class="w-5 h-5 text-grey-200 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </router-link>

          <router-link 
            to="/sales" 
            class="flex items-center gap-4 p-5 bg-gray-50 rounded-xl transition-all group"
          >
            <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-grey-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="font-bold text-gray-900 text-lg mb-1">Record Sales</h4>
              <p class="text-sm text-gray-600">Process new sales transactions</p>
            </div>
            <svg class="w-5 h-5 text-grey-200 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </router-link>

          <router-link 
            to="/transactions" 
            class="flex items-center gap-4 p-5 bg-gray-50 rounded-xl transition-all group"
          >
            <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
              <svg class="w-6 h-6 text-grey-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="font-bold text-gray-900 text-lg mb-1">Stock Transfers</h4>
              <p class="text-sm text-gray-600">Transfer items between locations</p>
            </div>
            <svg class="w-5 h-5 text-grey-200 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore()

const stats = ref({
  totalProducts: 0,
  lowStockItems: 0,
  inventoryValue: 0,
  totalStockUnits: 0,
  totalSales: 0,
  totalRevenue: 0,
  pendingTransfers: 0
})

const getUserRoleLabel = () => {
  const role = authStore.user?.role?.name;
  const labels = {
    'admin': 'Administrator',
    'branch_manager': 'Branch Manager',
    'store_manager': 'Store Manager'
  };
  return labels[role] || 'User';
}

const getCurrentDate = () => {
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  return new Date().toLocaleDateString('en-US', options);
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-US').format(num);
}

const fetchDashboardStats = async () => {
  try {
    const response = await fetch(`${import.meta.env.VITE_APP_API_BASE_URL || 'http://localhost:8000/api'}/dashboard-stats`, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json'
      }
    });

    if (response.ok) {
      const data = await response.json();
      stats.value = data;
    }
  } catch (error) {
    console.error('Error fetching dashboard stats:', error);
  }
}

onMounted(() => {
  fetchDashboardStats();
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap');

* {
  font-family: 'IBM Plex Sans', sans-serif;
}
</style>