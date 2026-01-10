<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Branch Inventory Overview</h1>
        <p class="text-gray-500 mt-1">Aggregate inventory view by branch and store</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg p-6 shadow-xs border border-gray-100">
        <p class="text-sm text-gray-500 mb-2">Total Branches</p>
        <p class="text-3xl font-bold text-gray-900">{{ branches.length }}</p>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-xs border border-gray-100">
        <p class="text-sm text-gray-500 mb-2">Total Stores</p>
        <p class="text-3xl font-bold text-gray-900">{{ stores.length }}</p>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-xs border border-gray-100">
        <p class="text-sm text-gray-500 mb-2">Total SKUs</p>
        <p class="text-3xl font-bold text-gray-900">{{ totalProducts }}</p>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-xs border border-gray-100">
        <p class="text-sm text-gray-500 mb-2">Total Inventory Value</p>
        <p class="text-3xl font-bold text-gray-900">KES {{ formatNumber(totalValue) }}</p>
      </div>
    </div>

    <div class="space-y-6">
      <div 
        v-for="branch in branches"
        :key="branch.id"
        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
      >
        <div class="bg-gradient-to-r from-[#2563eb] to-blue-600 p-6 text-white">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-2xl font-bold">{{ branch.name }}</h2>
              <p class="text-blue-100 text-sm mt-1">{{ branch.stores?.length || 0 }} stores</p>
            </div>
            <button 
              @click="toggleBranch(branch.id)"
              class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors"
            >
              <svg 
                :class="['w-5 h-5', expandedBranches.includes(branch.id) ? 'rotate-180' : '']" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
              </svg>
            </button>
          </div>
        </div>

        <div v-if="expandedBranches.includes(branch.id)" class="divide-y divide-gray-100">
          <div 
            v-for="store in branch.stores"
            :key="store.id"
            class="p-6 hover:bg-gray-50 transition-colors"
          >
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-bold text-gray-900">{{ store.name }}</h3>
              <button 
                @click="viewStoreInventory(store)"
                class="px-4 py-2 text-sm bg-[#2563eb]/10 text-[#2563eb] rounded-lg hover:bg-[#2563eb]/20 transition-colors font-medium"
              >
                View Details
              </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                <p class="text-xs text-gray-500">Total Products</p>
                <p class="text-2xl font-bold text-gray-900">{{ store.products?.length || 0 }}</p>
              </div>
              <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                <p class="text-xs text-gray-500">Total Units</p>
                <p class="text-2xl font-bold text-gray-900">{{ getTotalUnits(store) }}</p>
              </div>
              <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                <p class="text-xs text-gray-500">Total Value</p>
                <p class="text-2xl font-bold text-gray-900">KES {{ formatNumber(getStoreValue(store)) }}</p>
              </div>
            </div>

            <div v-if="store.products?.length" class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Product</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">SKU</th>
                    <th class="px-4 py-2 text-right font-semibold text-gray-700">Quantity</th>
                    <th class="px-4 py-2 text-right font-semibold text-gray-700">Unit Price</th>
                    <th class="px-4 py-2 text-right font-semibold text-gray-700">Total Value</th>
                    <th class="px-4 py-2 text-center font-semibold text-gray-700">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr v-for="product in store.products" :key="product.id" class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ product.name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ product.sku }}</td>
                    <td class="px-4 py-3 text-right text-gray-900 font-semibold">{{ product.quantity }}</td>
                    <td class="px-4 py-3 text-right text-gray-700">KES {{ formatNumber(product.retail_price) }}</td>
                    <td class="px-4 py-3 text-right text-gray-900 font-semibold">KES {{ formatNumber(product.quantity * product.retail_price) }}</td>
                    <td class="px-4 py-3 text-center">
                      <StatusBadge :status="getStockStatusColor(product.quantity)">
                        {{ product.quantity === 0 ? 'Out' : product.quantity < 10 ? 'Low' : 'In Stock' }}
                      </StatusBadge>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="text-center py-6 text-gray-500">
              No products in inventory
            </div>
          </div>
        </div>
      </div>
    </div>

    <Modal 
      v-if="showStoreModal"
      :title="`${selectedStore?.name} - Detailed Inventory`"
      @close="showStoreModal = false"
    >
      <div class="space-y-4">
        <div class="grid grid-cols-3 gap-4">
          <div class="p-3 bg-blue-50 rounded-lg">
            <p class="text-xs text-gray-500">Products</p>
            <p class="text-2xl font-bold">{{ selectedStore?.products?.length || 0 }}</p>
          </div>
          <div class="p-3 bg-green-50 rounded-lg">
            <p class="text-xs text-gray-500">Units</p>
            <p class="text-2xl font-bold">{{ getTotalUnits(selectedStore) }}</p>
          </div>
          <div class="p-3 bg-purple-50 rounded-lg">
            <p class="text-xs text-gray-500">Value</p>
            <p class="text-2xl font-bold">KES {{ formatNumber(getStoreValue(selectedStore)) }}</p>
          </div>
        </div>

        <div class="overflow-x-auto max-h-96 overflow-y-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 sticky top-0">
              <tr>
                <th class="px-3 py-2 text-left font-semibold text-gray-700">Product</th>
                <th class="px-3 py-2 text-right font-semibold text-gray-700">Qty</th>
                <th class="px-3 py-2 text-right font-semibold text-gray-700">Value</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="product in selectedStore?.products" :key="product.id" class="hover:bg-gray-50">
                <td class="px-3 py-2 font-medium text-gray-900">{{ product.name }}</td>
                <td class="px-3 py-2 text-right text-gray-900">{{ product.quantity }}</td>
                <td class="px-3 py-2 text-right font-semibold">KES {{ formatNumber(product.quantity * product.retail_price) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import Modal from '@/components/Modal.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import { api } from '@/lib/axios'

const branches = ref([])
const stores = ref([])  
const expandedBranches = ref([])
const showStoreModal = ref(false)
const selectedStore = ref(null)

const totalProducts = computed(() => {
  return stores.value.reduce((sum, store) => sum + (store.products?.length || 0), 0)
})

const totalValue = computed(() => {
  return stores.value.reduce((sum, store) => sum + getStoreValue(store), 0)
})

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-US').format(num)
}

const getTotalUnits = (store) => {
  return (store.products || []).reduce((sum, p) => sum + (p.quantity || 0), 0)
}

const getStoreValue = (store) => {
  return (store.products || []).reduce((sum, p) => sum + (p.quantity * p.retail_price), 0)
}

const getStockStatusColor = (quantity) => {
  if (quantity === 0) return 'error'
  if (quantity < 10) return 'warning'
  return 'success'
}

const toggleBranch = (branchId) => {
  const index = expandedBranches.value.indexOf(branchId)
  if (index > -1) {
    expandedBranches.value.splice(index, 1)
  } else {
    expandedBranches.value.push(branchId)
  }
}

const viewStoreInventory = (store) => {
  selectedStore.value = store
  showStoreModal.value = true
}

const fetchBranches = async () => {
  try {
    const response = await api.get('/branches')
    const data = response.data.data || response.data
    branches.value = data
    if (data.length > 0) {
      expandedBranches.value = [data[0].id]
    }
  } catch (error) {
    console.error('Error fetching branches:', error)
  }
}

const fetchStores = async () => {
  try {
    const response = await api.get('/stores')
    stores.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching stores:', error)
  }
}

onMounted(() => {
  fetchBranches()
  fetchStores()
})
</script>
