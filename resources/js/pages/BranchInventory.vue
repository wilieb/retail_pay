<template>
  <div class="p-6">
    <DataTable
      title="Branch Inventory"
      subtitle="Track and manage inventory across branches and stores"
      :columns="columns"
      :data="inventoryData"
      :branches="branches"
      :stores="stores"
      drawer-title="Store Inventory Details"
      @view="handleView"
    >
      <template #drawer-content="{ item }">
        <div class="space-y-6">
          <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-[#2563eb]/5 to-blue-50 rounded-xl border border-[#2563eb]/10">
            <div class="w-14 h-14 bg-[#2563eb] rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="text-xl font-bold text-gray-900 mb-1">{{ item.store_name }}</h4>
              <p class="text-sm text-gray-500">{{ item.branch_name }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-xl">
              <p class="text-sm text-gray-500 mb-1">Total Products</p>
              <p class="text-2xl font-bold text-gray-900">{{ item.product_count }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
              <p class="text-sm text-gray-500 mb-1">Total Units</p>
              <p class="text-2xl font-bold text-gray-900">{{ item.total_units }}</p>
            </div>
          </div>

          <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#10b981]/10 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-[#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <span class="text-sm font-medium text-gray-600">Total Inventory Value</span>
              </div>
              <span class="text-lg font-bold text-gray-900">KES {{ formatNumber(item.total_value) }}</span>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#fbbf24]/10 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-[#fbbf24]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                  </svg>
                </div>
                <span class="text-sm font-medium text-gray-600">Stock Status</span>
              </div>
              <span class="text-sm font-semibold" :class="getInventoryStatusClass(item.total_units)">
                {{ getInventoryStatus(item.total_units) }}
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
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import DataTable from '@/components/DataTables.vue'
import { api } from '@/lib/axios'

const authStore = useAuthStore()

const columns = [
  { key: 'store_name', label: 'Store Name' },
  { key: 'branch_name', label: 'Branch' },
  { key: 'product_count', label: 'Products', cellClass: 'text-center' },
  { key: 'total_units', label: 'Units', cellClass: 'text-right font-semibold' },
  { key: 'total_value', label: 'Total Value', cellClass: 'text-right font-semibold', format: (val) => `KES ${formatNumber(val)}` }
]

const inventoryData = ref([])
const branches = ref([])
const stores = ref([])

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-US').format(num)
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getInventoryStatus = (units) => {
  if (units === 0) return 'Out of Stock'
  if (units < 50) return 'Low Stock'
  if (units < 200) return 'Moderate'
  return 'Optimal'
}

const getInventoryStatusClass = (units) => {
  if (units === 0) return 'text-red-600 font-semibold'
  if (units < 50) return 'text-yellow-600 font-semibold'
  if (units < 200) return 'text-blue-600 font-semibold'
  return 'text-green-600 font-semibold'
}

const handleView = (item) => {
  // Drawer will be handled by DataTable component
}

const fetchInventory = async () => {
  try {
    const storesRes = await api.get('/stores')
    const branchesRes = await api.get('/branches')
    
    stores.value = storesRes.data.data || storesRes.data
    branches.value = branchesRes.data.data || branchesRes.data
    
    // Aggregate inventory by store
    inventoryData.value = stores.value.map(store => {
      const totalUnits = (store.products || []).reduce((sum, p) => sum + (p.quantity || 0), 0)
      const totalValue = (store.products || []).reduce((sum, p) => sum + (p.quantity * p.unit_price || 0), 0)
      
      return {
        id: store.id,
        store_name: store.name,
        branch_name: store.branch?.name || 'N/A',
        product_count: store.products?.length || 0,
        total_units: totalUnits,
        total_value: totalValue,
        updated_at: store.updated_at,
        ...store
      }
    })
  } catch (error) {
    console.error('Error fetching inventory:', error)
  }
}

onMounted(() => {
  fetchInventory()
})
</script>
