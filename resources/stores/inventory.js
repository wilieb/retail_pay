import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '@/lib/axios'

export const useInventoryStore = defineStore('inventory', () => {
  const inventories = ref([])
  const storeInventories = ref({})
  const branchInventories = ref({})
  const loading = ref(false)

  const fetchInventories = async () => {
    loading.value = true
    try {
      const response = await api.get('/inventories')
      inventories.value = response.data.data || response.data
    } catch (error) {
      console.error('Error fetching inventories:', error)
    } finally {
      loading.value = false
    }
  }

  const fetchStoreInventory = async (storeId) => {
    try {
      const response = await api.get(`/inventories/store/${storeId}`)
      storeInventories.value[storeId] = response.data.data || response.data
      return storeInventories.value[storeId]
    } catch (error) {
      console.error('Error fetching store inventory:', error)
    }
  }

  const fetchBranchInventory = async (branchId) => {
    try {
      const response = await api.get(`/inventories/branch/${branchId}`)
      branchInventories.value[branchId] = response.data.data || response.data
      return branchInventories.value[branchId]
    } catch (error) {
      console.error('Error fetching branch inventory:', error)
    }
  }

  const getTotalInventoryValue = (items) => {
    return items.reduce((sum, item) => sum + (item.quantity * item.retail_price), 0)
  }

  const getLowStockItems = (items, threshold = 10) => {
    return items.filter(item => item.quantity < threshold)
  }

  const getOutOfStockItems = (items) => {
    return items.filter(item => item.quantity === 0)
  }

  return {
    inventories,
    storeInventories,
    branchInventories,
    loading,
    fetchInventories,
    fetchStoreInventory,
    fetchBranchInventory,
    getTotalInventoryValue,
    getLowStockItems,
    getOutOfStockItems
  }
})
