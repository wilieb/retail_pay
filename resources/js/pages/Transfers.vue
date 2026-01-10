<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Stock Transfers</h1>
        <p class="text-gray-500 mt-1">Manage inter-branch and inter-store transfers with approval workflow</p>
      </div>
      <button 
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
      <div class="bg-white rounded-lg p-6 shadow-xs border border-gray-100">
        <p class="text-sm text-gray-500 mb-1">Pending Approval</p>
        <p class="text-3xl font-bold text-[#fbbf24]">{{ transfers.filter(t => t.status === 'pending').length }}</p>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-xs border border-gray-100">
        <p class="text-sm text-gray-500 mb-1">Approved</p>
        <p class="text-3xl font-bold text-[#10b981]">{{ transfers.filter(t => t.status === 'approved').length }}</p>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-xs border border-gray-100">
        <p class="text-sm text-gray-500 mb-1">Rejected</p>
        <p class="text-3xl font-bold text-[#ef4444]">{{ transfers.filter(t => t.status === 'rejected').length }}</p>
      </div>
      <div class="bg-white rounded-lg p-6 shadow-xs border border-gray-100">
        <p class="text-sm text-gray-500 mb-1">Completed</p>
        <p class="text-3xl font-bold text-[#2563eb]">{{ transfers.filter(t => t.status === 'completed').length }}</p>
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
      @submit="saveTransfer"
    >
      <div class="space-y-4">
        <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
          <p class="text-sm text-blue-700">Transfers require approval from a manager or administrator before completion.</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">From Store</label>
          <select 
            v-model="newTransfer.from_store_id"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
          >
            <option value="">Select Store</option>
            <option v-for="store in stores" :key="store.id" :value="store.id">
              {{ store.name }} ({{ store.branch?.name }})
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">To Store</label>
          <select 
            v-model="newTransfer.to_store_id"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
          >
            <option value="">Select Store</option>
            <option v-for="store in stores" :key="store.id" :value="store.id" :disabled="store.id == newTransfer.from_store_id">
              {{ store.name }} ({{ store.branch?.name }})
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
          <select 
            v-model="newTransfer.product_id"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
          >
            <option value="">Select Product</option>
            <option v-for="product in products" :key="product.id" :value="product.id">
              {{ product.name }} (Qty: {{ product.quantity }})
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
          <input 
            v-model="newTransfer.quantity"
            type="number"
            min="1"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
            placeholder="Enter quantity"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
          <textarea 
            v-model="newTransfer.notes"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
            placeholder="Add any notes (optional)"
            rows="3"
          />
        </div>
      </div>
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
import { api } from '@/lib/axios'

const authStore = useAuthStore()
const transfers = ref([])
const stores = ref([])
const products = ref([])
const showNewTransferModal = ref(false)
const showDetailsModal = ref(false)
const selectedTransfer = ref(null)
const filterStatus = ref('')

const newTransfer = ref({
  from_store_id: '',
  to_store_id: '',
  product_id: '',
  quantity: '',
  notes: ''
})

const filteredTransfers = computed(() => {
  if (!filterStatus.value) return transfers.value
  return transfers.value.filter(t => t.status === filterStatus.value)
})

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
  try {
    await api.post('/transfers', newTransfer.value)
    showNewTransferModal.value = false
    newTransfer.value = { from_store_id: '', to_store_id: '', product_id: '', quantity: '', notes: '' }
    fetchTransfers()
  } catch (error) {
    console.error('Error creating transfer:', error)
  }
}

const approveTransfer = async (transfer) => {
  try {
    await api.put(`/transfers/${transfer.id}/approve`)
    fetchTransfers()
  } catch (error) {
    console.error('Error approving transfer:', error)
  }
}

const rejectTransfer = async (transfer) => {
  const reason = prompt('Enter rejection reason:')
  if (!reason) return
  
  try {
    await api.put(`/transfers/${transfer.id}/reject`, { rejection_reason: reason })
    fetchTransfers()
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
