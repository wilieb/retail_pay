<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Stores</h1>
        <p class="text-gray-500 mt-1">Manage store locations and inventory</p>
      </div>
      <button 
        @click="showAddModal = true"
        class="flex items-center gap-2 px-6 py-2.5 bg-[#2563eb] text-white rounded-xl hover:bg-[#1d4ed8] transition-colors font-medium"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Store
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div 
        v-for="store in stores"
        :key="store.id"
        class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 group"
      >
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="text-lg font-bold text-gray-900">{{ store.name }}</h3>
              <p class="text-sm text-gray-500 mt-1">Store ID: {{ store.id }}</p>
            </div>
            <div class="w-10 h-10 bg-[#2563eb]/10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5 text-[#2563eb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
            </div>
          </div>

          <div class="space-y-3 mb-6">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <span class="text-sm text-gray-600">Branch</span>
              <span class="font-bold text-gray-700">{{ store.branch?.name || 'N/A' }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <span class="text-sm text-gray-600">Products</span>
              <span class="font-bold text-gray-900">{{ store.products?.length || 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <span class="text-sm text-gray-600">Users</span>
              <span class="font-bold text-gray-900">{{ store.users?.length || 0 }}</span>
            </div>
          </div>

          <div class="flex gap-2">
            <button 
              @click="editStore(store)"
              class="flex-1 px-4 py-2 bg-[#2563eb]/10 text-[#2563eb] rounded-lg hover:bg-[#2563eb]/20 transition-colors font-medium text-sm"
            >
              Edit
            </button>
            <button 
              @click="viewInventory(store)"
              class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm"
            >
              Inventory
            </button>
            <button 
              @click="deleteStore(store.id)"
              class="flex-1 px-4 py-2 bg-red-500/10 text-red-500 rounded-lg hover:bg-red-500/20 transition-colors font-medium text-sm"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <Modal 
      v-if="showAddModal"
      :title="formData.id ? 'Edit Store' : 'Add Store'"
      @close="showAddModal = false; formData = { id: null, name: '', branch_id: '' }"
      @submit="saveStore"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Store Name</label>
          <input 
            v-model="formData.name"
            type="text"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
            placeholder="Enter store name"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Branch</label>
          <select 
            v-model="formData.branch_id"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
          >
            <option value="">Select Branch</option>
            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
              {{ branch.name }}
            </option>
          </select>
        </div>
      </div>
    </Modal>

    <Modal 
      v-if="showInventoryModal"
      title="Store Inventory"
      @close="showInventoryModal = false"
    >
      <div class="space-y-4">
        <div class="p-4 bg-gray-50 rounded-lg mb-4">
          <p class="text-sm text-gray-500 mb-1">Store</p>
          <p class="text-lg font-bold text-gray-900">{{ selectedStore?.name }}</p>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Product</th>
                <th class="px-4 py-2 text-right font-semibold text-gray-700">Quantity</th>
                <th class="px-4 py-2 text-right font-semibold text-gray-700">Value</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="product in selectedStore?.products" :key="product.id" class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900">{{ product.name }}</td>
                <td class="px-4 py-3 text-right text-gray-700">{{ product.quantity }}</td>
                <td class="px-4 py-3 text-right text-gray-900 font-semibold">KES {{ formatNumber(product.quantity * product.retail_price) }}</td>
              </tr>
              <tr v-if="!selectedStore?.products?.length">
                <td colspan="3" class="px-4 py-3 text-center text-gray-500">No products in inventory</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Modal from '@/components/Modal.vue'
import { api } from '@/lib/axios'

const stores = ref([])
const branches = ref([])
const showAddModal = ref(false)
const showInventoryModal = ref(false)
const selectedStore = ref(null)
const formData = ref({ id: null, name: '', branch_id: '' })

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-US').format(num)
}

const fetchStores = async () => {
  try {
    const response = await api.get('/stores')
    stores.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching stores:', error)
  }
}

const fetchBranches = async () => {
  try {
    const response = await api.get('/branches')
    branches.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching branches:', error)
  }
}

const saveStore = async () => {
  try {
    if (formData.value.id) {
      const { id, ...data } = formData.value
      await api.put(`/stores/${id}`, data)
    } else {
      await api.post('/stores', { name: formData.value.name, branch_id: formData.value.branch_id })
    }
    showAddModal.value = false
    formData.value = { id: null, name: '', branch_id: '' }
    fetchStores()
  } catch (error) {
    console.error('Error saving store:', error)
  }
}

const editStore = (store) => {
  formData.value = { id: store.id, name: store.name, branch_id: store.branch_id }
  showAddModal.value = true
}

const deleteStore = async (id) => {
  if (confirm('Are you sure you want to delete this store?')) {
    try {
      await api.delete(`/stores/${id}`)
      fetchStores()
    } catch (error) {
      console.error('Error deleting store:', error)
    }
  }
}

const viewInventory = (store) => {
  selectedStore.value = store
  showInventoryModal.value = true
}

onMounted(() => {
  fetchStores()
  fetchBranches()
})
</script>
