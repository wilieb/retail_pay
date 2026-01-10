<template>
  <div class="p-6">
    <DataTable
      title="Inventory Management"
      subtitle="Track and manage stock levels across locations"
      :columns="columns"
      :data="inventoryData"
      :branches="branches"
      :stores="stores"
      drawer-title="Inventory Details"
      @view="handleView"
    >
      <template #drawer-content="{ item }">
        <div class="space-y-6">
          <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-[#2563eb]/5 to-blue-50 rounded-xl border border-[#2563eb]/10">
            <div class="w-14 h-14 bg-[#2563eb] rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="text-xl font-bold text-gray-900 mb-1">{{ item.product_name }}</h4>
              <p class="text-sm text-gray-500">SKU: {{ item.sku }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-xl">
              <p class="text-sm text-gray-500 mb-1">Total Quantity</p>
              <p class="text-2xl font-bold text-gray-900">{{ item.quantity }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
              <p class="text-sm text-gray-500 mb-1">Remaining Stock</p>
              <p class="text-2xl font-bold" :class="getStockColorClass(item.remaining_stock, item.quantity)">
                {{ item.remaining_stock }}
              </p>
            </div>
          </div>

          <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#fbbf24]/10 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-[#fbbf24]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                </div>
                <span class="text-sm font-medium text-gray-600">Branch</span>
              </div>
              <span class="text-sm font-semibold text-gray-900">{{ item.branch_name || 'N/A' }}</span>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#7c3aed]/10 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                  </svg>
                </div>
                <span class="text-sm font-medium text-gray-600">Store</span>
              </div>
              <span class="text-sm font-semibold text-gray-900">{{ item.store_name || 'N/A' }}</span>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#10b981]/10 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-[#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                  </svg>
                </div>
                <span class="text-sm font-medium text-gray-600">Stock Status</span>
              </div>
              <span class="text-sm font-semibold" :class="getStockColorClass(item.remaining_stock, item.quantity)">
                {{ getStockStatus(item.remaining_stock, item.quantity) }}
              </span>
            </div>
          </div>

          <div class="pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 mb-2">Last Updated</p>
            <p class="text-sm text-gray-600">{{ formatDate(item.updated_at) }}</p>
          </div>
        </div>
      </template>
    </DataTable>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import DataTable from '@/components/DataTables.vue'

const authStore = useAuthStore()

const columns = [
  { key: 'product_name', label: 'Product Name' },
  { key: 'sku', label: 'SKU' },
  { key: 'quantity', label: 'Quantity', cellClass: 'text-gray-900 font-semibold' },
  { 
    key: 'remaining_stock', 
    label: 'Remaining Stock',
    cellClass: (item) => getStockColorClass(item.remaining_stock, item.quantity)
  },
  { key: 'action', label: 'Action' }
]

const inventoryData = ref([])
const branches = ref([])
const stores = ref([])

const getStockColorClass = (remaining, total) => {
  const percentage = (remaining / total) * 100
  if (percentage <= 20) return 'text-[#f43f5e] font-bold'
  if (percentage <= 50) return 'text-[#fbbf24] font-bold'
  return 'text-[#10b981] font-bold'
}

const getStockStatus = (remaining, total) => {
  const percentage = (remaining / total) * 100
  if (percentage <= 20) return 'Critical'
  if (percentage <= 50) return 'Low'
  return 'Good'
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const handleView = (item) => {
  console.log('Viewing inventory item:', item)
}

const fetchInventoryData = async () => {
  try {
    const response = await fetch('/api/inventories', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json'
      }
    })
    
    if (response.ok) {
      const data = await response.json()
      inventoryData.value = data
    }
  } catch (error) {
    console.error('Error fetching inventory data:', error)
  }
}

const fetchBranches = async () => {
  try {
    const response = await fetch('/api/branches', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json'
      }
    })
    
    if (response.ok) {
      const data = await response.json()
      branches.value = data
    }
  } catch (error) {
    console.error('Error fetching branches:', error)
  }
}

const fetchStores = async () => {
  try {
    const response = await fetch('/api/stores', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json'
      }
    })
    
    if (response.ok) {
      const data = await response.json()
      stores.value = data
    }
  } catch (error) {
    console.error('Error fetching stores:', error)
  }
}

onMounted(() => {
  fetchInventoryData()
  fetchBranches()
  fetchStores()
})
</script>