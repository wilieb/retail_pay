<template>
  <div class="p-6">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Products</h1>
      <p class="text-gray-500 mt-1">Manage your product catalog</p>
    </div>

    <DataTable
      title=""
      subtitle=""
      :columns="columns"
      :data="productsData"
      drawer-title="Product Details"
      @view="handleView"
    >
      <template #drawer-content="{ item }">
        <div class="space-y-6">
          <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-[#7c3aed]/5 to-purple-50 rounded-xl border border-[#7c3aed]/10">
            <div class="w-14 h-14 bg-[#7c3aed] rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="text-xl font-bold text-gray-900 mb-1">{{ item.product_name }}</h4>
              <p class="text-sm text-gray-500">SKU: {{ item.sku }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-gradient-to-br from-[#2563eb]/5 to-blue-50 rounded-xl border border-[#2563eb]/10">
              <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Unit Price</p>
              <p class="text-2xl font-bold text-gray-900">KES {{ formatNumber(item.unit_price) }}</p>
            </div>

            <div class="p-4 bg-gradient-to-br from-[#10b981]/5 to-emerald-50 rounded-xl border border-[#10b981]/10">
              <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Retail Price</p>
              <p class="text-2xl font-bold text-gray-900">KES {{ formatNumber(item.retail_price) }}</p>
            </div>
          </div>

          <div class="p-4 bg-gradient-to-br from-[#fbbf24]/5 to-amber-50 rounded-xl border border-[#fbbf24]/10">
            <div class="flex items-center justify-between">
              <p class="text-sm text-gray-600 font-medium">Total Stock</p>
              <p class="text-2xl font-bold text-gray-900">{{ formatNumber(item.stock) }}</p>
            </div>
          </div>

          <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <span class="text-sm font-medium text-gray-600">Category</span>
              <span class="text-sm font-semibold text-gray-900">{{ item.category || 'N/A' }}</span>
            </div>

            <div v-if="item.store_name" class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <span class="text-sm font-medium text-gray-600">Store</span>
              <span class="text-sm font-semibold text-gray-900">{{ item.store_name }}</span>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <span class="text-sm font-medium text-gray-600">Margin</span>
              <span class="text-sm font-semibold text-gray-900">{{ Math.round(((item.retail_price - item.unit_price) / item.unit_price) * 100) }}%</span>
            </div>
          </div>

          <div class="pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-2">Last Updated</p>
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
import { api } from '@/lib/axios'
import DataTable from '@/components/DataTables.vue'
import Modal from '@/components/Modal.vue'
import TextInput from '@/components/TextInput.vue'
import SelectInput from '@/components/SelectInput.vue'
import Button from '@/components/Button.vue'

const authStore = useAuthStore()

const columns = [
  { key: 'product_name', label: 'Product Name' },
  { key: 'sku', label: 'SKU' },
  { 
    key: 'stock', 
    label: 'Stock',
    format: (value) => formatNumber(value),
    cellClass: 'text-gray-900 font-semibold'
  },
  { 
    key: 'unit_price', 
    label: 'Unit Price',
    format: (value) => `KES ${formatNumber(value)}`,
    cellClass: 'text-[#2563eb] font-bold'
  },
  { 
    key: 'retail_price', 
    label: 'Retail Price',
    format: (value) => `KES ${formatNumber(value)}`,
    cellClass: 'text-[#10b981] font-bold'
  },
  { key: 'action', label: 'Action' }
]

const productsData = ref([])
const stores = ref([])
const showModal = ref(false)
const formData = ref({ id: null, product_name: '', sku: '', store_id: '', unit_price: '', retail_price: '', stock: '', category: '' })

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-US').format(num)
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

const openModal = (product = null) => {
  if (product) {
    formData.value = { ...product }
  } else {
    formData.value = { id: null, product_name: '', sku: '', store_id: '', unit_price: '', retail_price: '', stock: '', category: '' }
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  formData.value = { id: null, product_name: '', sku: '', store_id: '', unit_price: '', retail_price: '', stock: '', category: '' }
}

const saveProduct = async () => {
  try {
    if (formData.value.id) {
      const { id, ...data } = formData.value
      await api.put(`/products/${id}`, data)
    } else {
      await api.post('/products', formData.value)
    }
    closeModal()
    fetchProductsData()
  } catch (error) {
    console.error('Error saving product:', error)
  }
}

const deleteProduct = async (id) => {
  if (confirm('Are you sure you want to delete this product?')) {
    try {
      await api.delete(`/products/${id}`)
      fetchProductsData()
    } catch (error) {
      console.error('Error deleting product:', error)
    }
  }
}

const handleView = (item) => {
  console.log('Viewing product:', item)
}

const fetchProductsData = async () => {
  try {
    const response = await api.get('/products')
    productsData.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching products:', error)
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
  fetchProductsData()
  fetchStores()
})
</script>