<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Stock Transfers</h1>
        <p class="text-gray-500 mt-1">Manage inter-branch and inter-store transfers with approval workflow</p>
      </div>
      <button 
        v-if="canCreateTransfer"
        @click="showNewTransferModal = true"
        class="flex items-center gap-2 px-6 py-2.5 bg-[#2563eb] text-white rounded-xl hover:bg-[#1d4ed8] transition-colors font-medium"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Transfer
      </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-black rounded-lg p-6 shadow-xs border border-gray-900">
        <p class="text-sm text-gray-400 mb-1">Pending Approval</p>
        <p class="text-3xl font-bold text-white">{{ transfers.filter(t => t.status === 'pending').length }}</p>
      </div>
      <div class="bg-black rounded-lg p-6 shadow-xs border border-gray-900">
        <p class="text-sm text-gray-400 mb-1">Approved</p>
        <p class="text-3xl font-bold text-white">{{ transfers.filter(t => t.status === 'approved').length }}</p>
      </div>
      <div class="bg-black rounded-lg p-6 shadow-xs border border-gray-900">
        <p class="text-sm text-gray-400 mb-1">Rejected</p>
        <p class="text-3xl font-bold text-white">{{ transfers.filter(t => t.status === 'rejected').length }}</p>
      </div>
      <div class="bg-black rounded-lg p-6 shadow-xs border border-gray-900">
        <p class="text-sm text-gray-400 mb-1">Completed</p>
        <p class="text-3xl font-bold text-white">{{ transfers.filter(t => t.status === 'completed').length }}</p>
      </div>
    </div>

    <div class="space-y-4">
      <div class="flex gap-2">
        <button 
          v-for="status in ['pending', 'approved', 'rejected', 'completed']"
          :key="status"
          @click="filterStatus = filterStatus === status ? '' : status"
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

      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Transfer ID</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">From</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">To</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Product</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Quantity</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="transfer in filteredTransfers" :key="transfer.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ transfer.id }}</td>
                <td class="px-6 py-4 text-sm text-gray-700">{{ transfer.fromStore?.name || transfer.fromBranch?.name }}</td>
                <td class="px-6 py-4 text-sm text-gray-700">{{ transfer.toStore?.name || transfer.toBranch?.name }}</td>
                <td class="px-6 py-4 text-sm text-gray-700">{{ transfer.product?.name || 'N/A' }}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ transfer.quantity }}</td>
                <td class="px-6 py-4">
                  <StatusBadge :status="getTransferStatus(transfer.status)">
                    {{ transfer.status }}
                  </StatusBadge>
                </td>
                <td class="px-6 py-4">
                  <div class="flex gap-2">
                    <button 
                      v-if="transfer.status === 'pending' && canApprove()"
                      @click="approveTransfer(transfer)"
                      class="px-3 py-1.5 text-sm bg-[#10b981]/10 text-[#10b981] rounded-lg hover:bg-[#10b981]/20 transition-colors font-medium"
                    >
                      Approve
                    </button>
                    <button 
                      v-if="transfer.status === 'pending' && canApprove()"
                      @click="rejectTransfer(transfer)"
                      class="px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors font-medium"
                    >
                      Reject
                    </button>
                    <button 
                      @click="viewDetails(transfer)"
                      class="px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                    >
                      View
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="!filteredTransfers.length">
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                  No transfers found
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <Modal 
      v-if="showNewTransferModal"
      title="Create Transfer Request"
      @close="showNewTransferModal = false"
    >
      <div class="space-y-4">
        <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
          <p class="text-sm text-blue-700">Transfers require approval from a manager or administrator before completion.</p>
        </div>

        <SelectInput
          v-model="newTransfer.from_store_id"
          label="From Store"
          placeholder="Select store"
          :error="errors.from_store_id"
          :required="true"
          :options="stores.map(s => ({ value: s.id, label: `${s.name} (${s.branch?.name || 'N/A'})` }))"
          @update:modelValue="validateTransferForm"
        />

        <SelectInput
          v-model="newTransfer.to_store_id"
          label="To Store"
          placeholder="Select store"
          :error="errors.to_store_id"
          :required="true"
          :options="stores.filter(s => s.id !== newTransfer.from_store_id).map(s => ({ value: s.id, label: `${s.name} (${s.branch?.name || 'N/A'})` }))"
          @update:modelValue="validateTransferForm"
        />

        <SelectInput
          v-model="newTransfer.product_id"
          label="Product"
          placeholder="Select product"
          :error="errors.product_id"
          :required="true"
          :options="availableProducts.map(p => ({ value: p.id, label: `${p.name} (SKU: ${p.sku}, Available: ${p.quantity})` }))"
          @update:modelValue="validateTransferForm"
        />

        <TextInput
          v-model.number="newTransfer.quantity"
          label="Quantity"
          type="number"
          placeholder="Enter quantity"
          :error="errors.quantity"
          :required="true"
          @update:modelValue="validateTransferForm"
        />

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
          <textarea 
            v-model="newTransfer.notes"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
            placeholder="Add any notes"
            rows="3"
          />
        </div>
      </div>
      <template #footer>
        <div class="flex justify-end gap-3">
          <button 
            @click="showNewTransferModal = false"
            class="px-6 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition"
          >
            Cancel
          </button>
          <button 
            @click="saveTransfer"
            :disabled="!isTransferFormValid || loadingTransfer"
            class="px-6 py-2.5 bg-[#2563eb] hover:bg-[#1d4ed8] disabled:bg-gray-300 text-white rounded-lg font-medium transition"
          >
            {{ loadingTransfer ? 'Creating...' : 'Create Transfer' }}
          </button>
        </div>
      </template>
    </Modal>

    <Modal 
      v-if="showDetailsModal"
      title="Transfer Details"
      @close="showDetailsModal = false"
    >
      <div class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
          <div class="p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-500 mb-1">Transfer ID</p>
            <p class="text-lg font-bold text-gray-900">#{{ selectedTransfer?.id }}</p>
          </div>
          <div class="p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-500 mb-1">Status</p>
            <StatusBadge :status="getTransferStatus(selectedTransfer?.status)">
              {{ selectedTransfer?.status }}
            </StatusBadge>
          </div>
        </div>

        <div class="space-y-3">
          <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-100">
            <span class="font-medium text-gray-700">From</span>
            <span class="text-gray-900 font-bold">{{ selectedTransfer?.fromStore?.name || 'N/A' }}</span>
          </div>
          <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-100">
            <span class="font-medium text-gray-700">To</span>
            <span class="text-gray-900 font-bold">{{ selectedTransfer?.toStore?.name || 'N/A' }}</span>
          </div>
        </div>

        <div class="p-4 bg-gray-50 rounded-lg">
          <p class="text-sm text-gray-500 mb-2">Product</p>
          <p class="text-lg font-bold text-gray-900">{{ selectedTransfer?.product?.name }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-500 mb-1">Quantity</p>
            <p class="text-2xl font-bold text-gray-900">{{ selectedTransfer?.quantity }}</p>
          </div>
          <div class="p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-500 mb-1">Requested By</p>
            <p class="text-lg font-bold text-gray-900">{{ selectedTransfer?.requestedBy?.name || 'N/A' }}</p>
          </div>
        </div>

        <div v-if="selectedTransfer?.notes" class="p-4 bg-yellow-50 rounded-lg border border-yellow-100">
          <p class="text-sm text-gray-500 mb-2">Notes</p>
          <p class="text-gray-700">{{ selectedTransfer.notes }}</p>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import Modal from '@/components/Modal.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import SelectInput from '@/components/SelectInput.vue'
import TextInput from '@/components/TextInput.vue'
import { api } from '@/lib/axios'

const authStore = useAuthStore()
const transfers = ref([])
const stores = ref([])
const products = ref([])
const showNewTransferModal = ref(false)
const showDetailsModal = ref(false)
const selectedTransfer = ref(null)
const filterStatus = ref('')
const loadingTransfer = ref(false)

const newTransfer = ref({
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

const filteredTransfers = computed(() => {
  if (!filterStatus.value) return transfers.value
  return transfers.value.filter(t => t.status === filterStatus.value)
})

const availableProducts = computed(() => {
  // Return products from the selected 'from' store
  if (!newTransfer.value.from_store_id) return []
  const fromStore = stores.value.find(s => s.id === parseInt(newTransfer.value.from_store_id))
  return fromStore?.products || []
})

const isTransferFormValid = computed(() => {
  return newTransfer.value.from_store_id && 
         newTransfer.value.to_store_id && 
         newTransfer.value.from_store_id !== newTransfer.value.to_store_id &&
         newTransfer.value.product_id && 
         newTransfer.value.quantity && 
         newTransfer.value.quantity > 0
})

const validateTransferForm = () => {
  errors.value = {}
  if (!newTransfer.value.from_store_id) errors.value.from_store_id = 'From store is required'
  if (!newTransfer.value.to_store_id) errors.value.to_store_id = 'To store is required'
  if (newTransfer.value.from_store_id === newTransfer.value.to_store_id) {
    errors.value.to_store_id = 'Cannot transfer to the same store'
  }
  if (!newTransfer.value.product_id) errors.value.product_id = 'Product is required'
  if (!newTransfer.value.quantity || newTransfer.value.quantity <= 0) {
    errors.value.quantity = 'Quantity must be greater than 0'
  }
}

const getTransferStatus = (status) => {
  const statusMap = {
    'pending': 'warning',
    'approved': 'success',
    'rejected': 'error',
    'completed': 'info'
  }
  return statusMap[status] || 'secondary'
}

const canApprove = () => {
  const role = authStore.user?.role?.name
  return role === 'admin' || role === 'branch_manager'
}

const canCreateTransfer = computed(() => {
  const role = authStore.user?.role?.name
  return role === 'admin' || role === 'branch_manager' || role === 'store_manager'
})

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

const fetchProducts = async () => {
  try {
    const response = await api.get('/products')
    products.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching products:', error)
  }
}

const saveTransfer = async () => {
  validateTransferForm()
  if (!isTransferFormValid.value) return
  
  loadingTransfer.value = true
  try {
    await api.post('/transfers', newTransfer.value)
    showNewTransferModal.value = false
    newTransfer.value = { from_store_id: '', to_store_id: '', product_id: '', quantity: '', notes: '' }
    errors.value = {}
    await fetchTransfers()
  } catch (error) {
    console.error('Error creating transfer:', error)
  } finally {
    loadingTransfer.value = false
  }
}

const approveTransfer = async (transfer) => {
  try {
    await api.put(`/transfers/${transfer.id}/approve`)
    await fetchTransfers()
  } catch (error) {
    console.error('Error approving transfer:', error)
  }
}

const rejectTransfer = async (transfer) => {
  const reason = prompt('Enter rejection reason:')
  if (!reason) return
  
  try {
    await api.put(`/transfers/${transfer.id}/reject`, { rejection_reason: reason })
    await fetchTransfers()
  } catch (error) {
    console.error('Error rejecting transfer:', error)
  }
}

const viewDetails = (transfer) => {
  selectedTransfer.value = transfer
  showDetailsModal.value = true
}

onMounted(() => {
  fetchTransfers()
  fetchStores()
  fetchProducts()
})
</script>
