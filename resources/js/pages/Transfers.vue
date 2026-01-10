<template>
  <div class="p-6">
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Stock Transfers</h1>
        <p class="text-gray-500 mt-1">Manage inter-branch and inter-store transfers with approval workflow</p>
      </div>
      <button 
        v-if="canCreateTransfer"
        @click="openTransferModal"
        class="bg-[#2563eb] hover:bg-[#1d4ed8] text-white px-4 py-2.5 rounded-lg flex items-center gap-2 font-medium transition"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Transfer
      </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg p-6 shadow-xs border border-gray-100">
        <p class="text-sm text-gray-400 mb-1">Pending Approval</p>
        <p class="text-3xl font-bold text-gray-600">{{ transfers.filter(t => t.status === 'pending').length }}</p>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-xs border border-gray-100">
        <p class="text-sm text-gray-400 mb-1">Approved</p>
        <p class="text-3xl font-bold text-gray-600">{{ transfers.filter(t => t.status === 'approved').length }}</p>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-xs border border-gray-100">
        <p class="text-sm text-gray-400 mb-1">Rejected</p>
        <p class="text-3xl font-bold text-gray-600">{{ transfers.filter(t => t.status === 'rejected').length }}</p>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-xs border border-gray-100">
        <p class="text-sm text-gray-400 mb-1">Completed</p>
        <p class="text-3xl font-bold text-gray-600">{{ transfers.filter(t => t.status === 'completed').length }}</p>
      </div>
    </div>

    <div class="mb-4 flex gap-2">
      <button 
        @click="filterStatus = ''"
        :class="[
          'px-4 py-2 rounded-lg font-medium transition-colors text-sm',
          filterStatus === '' 
            ? 'bg-[#2563eb] text-white' 
            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
        ]"
      >
        All
      </button>
      <button 
        v-for="status in ['pending', 'approved', 'rejected', 'completed']"
        :key="status"
        @click="filterStatus = status"
        :class="[
          'px-4 py-2 rounded-lg font-medium transition-colors text-sm',
          filterStatus === status 
            ? 'bg-[#2563eb] text-white' 
            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
        ]"
      >
        {{ status.charAt(0).toUpperCase() + status.slice(1) }}
      </button>
    </div>

    <DataTable
      title=""
      subtitle=""
      :columns="columns"
      :data="filteredTransfers"
      drawer-title="Transfer Details"
      @view="viewDetails"
      @approve="handleApprove"
      @reject="handleReject"
      ref="dataTableRef"
    >
      <template #drawer-content="{ item }">
        <div class="h-full bg-white flex flex-col">
          <div class="bg-gray-50 px-6 py-8 text-gray-900">
            <div class="flex items-center justify-between mb-6">
              <div>
                <p class="text-xs uppercase tracking-wider opacity-90 font-medium">Transfer ID</p>
                <h1 class="text-2xl font-bold mt-1 tabular-nums">{{ item.transfer_id }}</h1>
              </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-white/20">
              <div>
                <p class="text-xs opacity-75">Requested By</p>
                <p class="font-medium">{{ item.requested_by?.name || 'N/A' }}</p>
              </div>
              <div class="text-right">
                <p class="text-xs opacity-75">Status</p>
                <span :class="[
                  'inline-flex items-center px-2 py-1 text-xs font-medium rounded mt-1',
                  item.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                  item.status === 'approved' ? 'bg-green-100 text-green-800' :
                  item.status === 'rejected' ? 'bg-red-100 text-red-800' :
                  'bg-blue-100 text-blue-800'
                ]">
                  {{ item.status }}
                </span>
              </div>
            </div>
          </div>

          <div class="flex-1 overflow-y-auto px-1 py-6 space-y-6">
            <div>
              <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Transfer Route</h2>
              <div class="space-y-3">
                <div v-if="item.approved_by" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-blue-100">
                  <span class="font-medium text-gray-700">Approved By</span>
                  <span class="text-gray-900 font-bold">{{ item.approved_by?.name || 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-blue-100">
                  <span class="font-medium text-gray-700">From</span>
                  <span class="text-gray-900 font-bold">{{ item.from_store?.name || item.from_branch?.name || 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-green-100">
                  <span class="font-medium text-gray-700">To</span>
                  <span class="text-gray-900 font-bold">{{ item.to_store?.name || item.to_branch?.name || 'N/A' }}</span>
                </div>
              </div>
            </div>

            <div>
              <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Product Details</h2>
              <div class="bg-gray-50 rounded-lg border border-gray-200 divide-y divide-gray-200">
                <div class="px-4 py-3 flex justify-between items-center">
                  <span class="text-sm text-gray-600">Product</span>
                  <span class="text-sm font-semibold text-gray-900">{{ item.product?.name || 'N/A' }}</span>
                </div>
                <div class="px-4 py-3 flex justify-between items-center">
                  <span class="text-sm text-gray-600">Quantity</span>
                  <span class="text-sm font-semibold text-gray-900 tabular-nums">{{ item.quantity }}</span>
                </div>
              </div>
            </div>

            <div v-if="item.notes">
              <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Notes</h2>
              <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                <p class="text-sm text-gray-700">{{ item.notes }}</p>
              </div>
            </div>

            <div v-if="item.status === 'pending' && canApprove()" class="pt-4 border-t border-gray-200">
              <div class="flex gap-3">
                <button 
                  @click="handleApprove(item)"
                  class="flex-1 bg-[#10b981] hover:bg-[#059669] text-white font-medium py-3 rounded-lg transition-colors"
                >
                  Approve Transfer
                </button>
                <button 
                  @click="handleReject(item)"
                  class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 rounded-lg transition-colors"
                >
                  Reject Transfer
                </button>
              </div>
            </div>
          </div>
        </div>
      </template>
    </DataTable>

    <Modal v-if="showTransferModal" @close="closeTransferModal" title="Create Transfer Request">
      <template #content>
        <div class="space-y-5">
          <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
            <p class="text-sm text-blue-700">Transfers require approval from a manager or administrator before completion.</p>
          </div>

          <SelectInput
            v-model="formData.from_store_id"
            label="From Store"
            placeholder="Select store"
            :error="errors.from_store_id"
            :required="true"
            :options="stores.map(s => ({ value: s.id, label: `${s.name} (${s.branch?.name || 'N/A'})` }))"
            @update:modelValue="onFromStoreChange"
          />

          <SelectInput
            v-model="formData.to_store_id"
            label="To Store"
            placeholder="Select store"
            :error="errors.to_store_id"
            :required="true"
            :disabled="!formData.from_store_id"
            :options="stores.filter(s => s.id != formData.from_store_id).map(s => ({ value: s.id, label: `${s.name} (${s.branch?.name || 'N/A'})` }))"
            @update:modelValue="validateForm"
          />

          <SelectInput
            v-model="formData.product_id"
            label="Product"
            placeholder="Select product"
            :error="errors.product_id"
            :required="true"
            :disabled="!formData.from_store_id"
            :options="availableProducts.map(p => ({ value: p.id, label: `${p.name} (SKU: ${p.sku}, Available: ${p.quantity})` }))"
            @update:modelValue="onProductChange"
          />

          <TextInput
            v-model.number="formData.quantity"
            label="Quantity"
            type="number"
            placeholder="Enter quantity"
            :error="errors.quantity"
            :required="true"
            :min="1"
            :max="selectedProductStock"
            @update:modelValue="validateForm"
          />

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
            <textarea 
              v-model="formData.notes"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
              placeholder="Add any notes"
              rows="3"
            />
          </div>

          <div v-if="errors.general" class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r">
            <p class="text-red-700 text-sm">{{ errors.general }}</p>
          </div>
        </div>
      </template>

      <template #footer>
        <div class="flex justify-end gap-3">
          <button 
            @click="closeTransferModal"
            class="px-6 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition"
          >
            Cancel
          </button>
          <button 
            @click="saveTransfer"
            :disabled="!isFormValid || loading"
            class="px-6 py-2.5 bg-[#2563eb] hover:bg-[#1d4ed8] disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-medium transition"
          >
            {{ loading ? 'Creating...' : 'Create Transfer' }}
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { api } from '@/lib/axios'
import DataTable from '@/components/DataTables.vue'
import Modal from '@/components/Modal.vue'
import SelectInput from '@/components/SelectInput.vue'
import TextInput from '@/components/TextInput.vue'

const authStore = useAuthStore()
const transfers = ref([])
const stores = ref([])
const showTransferModal = ref(false)
const filterStatus = ref('')
const loading = ref(false)

const formData = ref({
  from_store_id: '',
  to_store_id: '',
  product_id: '',
  quantity: '',
  notes: ''
})

const errors = ref({
  from_store_id: '',
  to_store_id: '',
  product_id: '',
  quantity: ''
})

const columns = [
  { key: 'transfer_id', label: 'Transfer ID', format: (value) => value || 'N/A' },
  { key: 'from_store', label: 'From', format: (value) => value?.name || 'N/A' },
  { key: 'to_store', label: 'To', format: (value) => value?.name || 'N/A' },
  { key: 'product', label: 'Product', format: (value) => value?.name || 'N/A' },
  { key: 'quantity', label: 'Quantity', cellClass: 'text-gray-900 font-semibold' },
  { 
    key: 'status', 
    label: 'Status',
    format: (value) => value.charAt(0).toUpperCase() + value.slice(1),
    cellClass: (row) => {
      const classes = {
        'pending': 'text-yellow-800',
        'approved': 'text-green-800',
        'rejected': 'text-red-800',
        'completed': 'text-blue-800'
      }
      return classes[row.status] || 'text-gray-800'
    }
  },
  { key: 'action', label: 'Actions' }
]

const filteredTransfers = computed(() => {
  if (!filterStatus.value) return transfers.value
  return transfers.value.filter(t => t.status === filterStatus.value)
})

const availableProducts = computed(() => {
  if (!formData.value.from_store_id) return []
  const fromStore = stores.value.find(s => s.id === parseInt(formData.value.from_store_id))
  return fromStore?.products || []
})

const selectedProductStock = computed(() => {
  if (!formData.value.product_id) return null
  const product = availableProducts.value.find(p => p.id == formData.value.product_id)
  return product ? product.quantity : null
})

const isFormValid = computed(() => {
  return formData.value.from_store_id && 
         formData.value.to_store_id && 
         formData.value.from_store_id !== formData.value.to_store_id &&
         formData.value.product_id && 
         formData.value.quantity && 
         formData.value.quantity > 0
})

const canCreateTransfer = computed(() => {
  const role = authStore.user?.role?.name
  return role === 'admin' || role === 'branch_manager' || role === 'store_manager'
})

const canApprove = () => {
  const role = authStore.user?.role?.name
  return role === 'admin' || role === 'branch_manager'
}

const onFromStoreChange = () => {
  formData.value.product_id = ''
  formData.value.quantity = ''
  errors.value.from_store_id = ''
  validateForm()
}

const onProductChange = () => {
  formData.value.quantity = ''
  errors.value.product_id = ''
  validateForm()
}

const validateForm = () => {
  errors.value = {}
  
  if (!formData.value.from_store_id) {
    errors.value.from_store_id = 'From store is required'
  }
  
  if (!formData.value.to_store_id) {
    errors.value.to_store_id = 'To store is required'
  }
  
  if (formData.value.from_store_id === formData.value.to_store_id) {
    errors.value.to_store_id = 'Cannot transfer to the same store'
  }
  
  if (!formData.value.product_id) {
    errors.value.product_id = 'Product is required'
  }
  
  if (!formData.value.quantity || formData.value.quantity <= 0) {
    errors.value.quantity = 'Quantity must be greater than 0'
  }

  const selectedProduct = availableProducts.value.find(p => p.id === formData.value.product_id)
  if (selectedProduct && formData.value.quantity > selectedProduct.quantity) {
    errors.value.quantity = `Only ${selectedProduct.quantity} units available in stock`
  }
}

const openTransferModal = () => {
  formData.value = { from_store_id: '', to_store_id: '', product_id: '', quantity: '', notes: '' }
  errors.value = {}
  showTransferModal.value = true
}

const closeTransferModal = () => {
  showTransferModal.value = false
  formData.value = { from_store_id: '', to_store_id: '', product_id: '', quantity: '', notes: '' }
  errors.value = {}
}

const saveTransfer = async () => {
  validateForm()
  if (!isFormValid.value || Object.keys(errors.value).length > 0) return
  
  loading.value = true
  try {
    const payload = {
      product_id: parseInt(formData.value.product_id),
      from_store_id: parseInt(formData.value.from_store_id),
      to_store_id: parseInt(formData.value.to_store_id),
      quantity: parseInt(formData.value.quantity),
      notes: formData.value.notes || ''
    }
    await api.post('/transfers', payload)
    closeTransferModal()
    await fetchTransfers()
  } catch (error) {
    errors.value.general = error.response?.data?.message || 'Error creating transfer'
  } finally {
    loading.value = false
  }
}

const handleApprove = async (transfer) => {
  try {
    await api.put(`/transfers/${transfer.id}/approve`)
    await fetchTransfers()
    if (dataTableRef.value && dataTableRef.value.closeDrawer) {
      dataTableRef.value.closeDrawer()
    }
  } catch (error) {
    console.error('Error approving transfer:', error)
  }
}

const handleReject = async (transfer) => {
  const reason = prompt('Enter rejection reason:')
  if (!reason) return
  
  try {
    await api.put(`/transfers/${transfer.id}/reject`, { rejection_reason: reason })
    await fetchTransfers()
    if (dataTableRef.value && dataTableRef.value.closeDrawer) {
      dataTableRef.value.closeDrawer()
    }
  } catch (error) {
    console.error('Error rejecting transfer:', error)
  }
}

const viewDetails = (item) => {
  console.log('Viewing transfer:', item)
}

const fetchTransfers = async () => {
  try {
    const response = await api.get('/transfers')
    transfers.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching transfers:', error)
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
  fetchTransfers()
  fetchStores()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&display=swap');

* {
  font-family: 'IBM Plex Sans', sans-serif;
}

.tabular-nums {
  font-family: 'IBM Plex Mono', monospace;
  font-variant-numeric: tabular-nums;
}
</style>