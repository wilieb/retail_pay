<template>
  <div class="p-6">
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Products</h1>
        <p class="text-gray-500 mt-1">Manage your product catalog</p>
      </div>
      <Button 
        @click="openModal()" 
        class="bg-[#7c3aed] hover:bg-[#6d28d9] text-white px-4 py-2 rounded-lg flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Product
      </Button>
    </div>

    <DataTable
      title=""
      subtitle=""
      :columns="columns"
      :data="productsData"
      drawer-title="Product Details"
      @view="handleView"
    >
      <template #action="{ item }">
        <div class="flex items-center gap-2">
          <button 
            @click="openModal(item)"
            class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-md"
          >
            Edit
          </button>
          <button 
            @click="deleteProduct(item.id)"
            class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-md"
          >
            Delete
          </button>
        </div>
      </template>

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
              <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-[#2563eb] rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <p class="text-sm text-gray-500 font-medium">Unit Price</p>
              </div>
              <p class="text-2xl font-bold text-gray-900">KES {{ formatNumber(item.unit_price) }}</p>
            </div>

            <div class="p-4 bg-gradient-to-br from-[#10b981]/5 to-emerald-50 rounded-xl border border-[#10b981]/10">
              <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-[#10b981] rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                  </svg>
                </div>
                <p class="text-sm text-gray-500 font-medium">Retail Price</p>
              </div>
              <p class="text-2xl font-bold text-gray-900">KES {{ formatNumber(item.retail_price) }}</p>
            </div>
          </div>

          <div class="p-4 bg-gradient-to-br from-[#fbbf24]/5 to-amber-50 rounded-xl border border-[#fbbf24]/10">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#fbbf24] rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                </div>
                <span class="text-sm font-medium text-gray-600">Total Stock</span>
              </div>
              <span class="text-2xl font-bold text-gray-900">{{ formatNumber(item.stock) }}</span>
            </div>
          </div>

          <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#f43f5e]/10 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-[#f43f5e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                </div>
                <span class="text-sm font-medium text-gray-600">Category</span>
              </div>
              <span class="text-sm font-semibold text-gray-900">{{ item.category || 'N/A' }}</span>
            </div>

            <div v-if="item.store_name" class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#7c3aed]/10 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                  </svg>
                </div>
                <span class="text-sm font-medium text-gray-600">Store</span>
              </div>
              <span class="text-sm font-semibold text-gray-900">{{ item.store_name }}</span>
            </div>
          </div>

          <div class="pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 mb-2">Last Updated</p>
            <p class="text-sm text-gray-600">{{ formatDate(item.updated_at) }}</p>
          </div>
        </div>
      </template>
    </DataTable>

    <Modal v-if="showModal" @close="closeModal">
      <template #header>
        {{ formData.id ? 'Edit Product' : 'Add Product' }}
      </template>
      <template #body>
        <div class="space-y-4">
          <TextInput 
            v-model="formData.product_name" 
            label="Product Name"
            placeholder="Enter product name"
          />
          <TextInput 
            v-model="formData.sku" 
            label="SKU"
            placeholder="Enter product SKU"
          />
          <SelectInput 
            v-model="formData.store_id" 
            label="Store"
            placeholder="Select store"
            :options="stores.map(s => ({ value: s.id, label: s.store_name }))"
          />
          <TextInput 
            v-model.number="formData.unit_price" 
            label="Unit Price"
            type="number"
            placeholder="Enter unit price"
          />
          <TextInput 
            v-model.number="formData.retail_price" 
            label="Retail Price"
            type="number"
            placeholder="Enter retail price"
          />
          <TextInput 
            v-model.number="formData.stock" 
            label="Stock"
            type="number"
            placeholder="Enter stock quantity"
          />
          <TextInput 
            v-model="formData.category" 
            label="Category"
            placeholder="Enter product category"
          />
        </div>
      </template>
      <template #footer>
        <div class="flex justify-end gap-2">
          <Button @click="closeModal" class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg">
            Cancel
          </Button>
          <Button @click="saveProduct" class="px-4 py-2 bg-[#7c3aed] hover:bg-[#6d28d9] text-white rounded-lg">
            Save
          </Button>
        </div>
      </template>
    </Modal>
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