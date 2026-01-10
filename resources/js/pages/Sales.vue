<template>
  <div class="p-6">
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Sales</h1>
        <p class="text-gray-500 mt-1">View all sales transactions history</p>
      </div>
      <Button 
        @click="openSalesModal()" 
        class="bg-[#10b981] hover:bg-[#059669] text-white px-4 py-2 rounded-lg flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Record Sale
      </Button>
    </div>

    <DataTable
      title=""
      subtitle=""
      :columns="columns"
      :data="transactionsData"
      drawer-title="Sales Details"
      @view="handleView"
    >
      <template #drawer-content="{ item }">
        <div class="space-y-6">
          <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-[#10b981]/5 to-emerald-50 rounded-xl border border-[#10b981]/10">
            <div class="w-14 h-14 bg-[#10b981] rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="text-xl font-bold text-gray-900 mb-1">Transaction #{{ item.transaction_id }}</h4>
              <p class="text-sm text-gray-500">{{ formatDate(item.transaction_date) }}</p>
            </div>
          </div>

          <div class="p-5 bg-gradient-to-br from-[#2563eb]/5 to-blue-50 rounded-xl border border-[#2563eb]/10">
            <div class="flex items-center gap-3 mb-3">
              <div class="w-10 h-10 bg-[#2563eb] rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
              </div>
              <p class="text-sm font-medium text-gray-600">Product</p>
            </div>
            <p class="text-lg font-bold text-gray-900">{{ item.product_name }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-[#7c3aed]/10 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-[#7c3aed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                  </svg>
                </div>
                <p class="text-sm text-gray-500 font-medium">Quantity</p>
              </div>
              <p class="text-2xl font-bold text-gray-900">{{ item.quantity }}</p>
            </div>

            <div class="p-4 bg-gradient-to-br from-[#fbbf24]/5 to-amber-50 rounded-xl border border-[#fbbf24]/10">
              <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-[#fbbf24] rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <p class="text-sm text-gray-500 font-medium">Amount</p>
              </div>
              <p class="text-2xl font-bold text-[#fbbf24]">KES {{ formatNumber(item.amount) }}</p>
            </div>
          </div>

          <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#2563eb]/10 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-[#2563eb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                </div>
                <span class="text-sm font-medium text-gray-600">Sold By</span>
              </div>
              <span class="text-sm font-semibold text-gray-900">{{ item.user_name || 'N/A' }}</span>
            </div>

            <div v-if="item.branch_name" class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#f43f5e]/10 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-[#f43f5e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                  </svg>
                </div>
                <span class="text-sm font-medium text-gray-600">Branch</span>
              </div>
              <span class="text-sm font-semibold text-gray-900">{{ item.branch_name }}</span>
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

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#10b981]/10 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-[#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <span class="text-sm font-medium text-gray-600">Status</span>
              </div>
              <span class="text-sm font-semibold text-[#10b981]">Completed</span>
            </div>
          </div>

          <div class="pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 mb-2">Transaction Date</p>
            <p class="text-sm text-gray-600">{{ formatFullDate(item.transaction_date) }}</p>
          </div>
        </div>
      </template>
    </DataTable>

    <Modal v-if="showSalesModal" @close="closeSalesModal">
      <template #header>
        Record Sale
      </template>
      <template #body>
        <div class="space-y-4">
          <SelectInput 
            v-model="formData.product_id" 
            label="Product"
            placeholder="Select product"
            :options="products.map(p => ({ value: p.id, label: p.product_name }))"
          />
          <SelectInput 
            v-model="formData.store_id" 
            label="Store"
            placeholder="Select store"
            :options="stores.map(s => ({ value: s.id, label: s.name }))"
          />
          <TextInput 
            v-model.number="formData.quantity" 
            label="Quantity"
            type="number"
            placeholder="Enter quantity"
          />
          <TextInput 
            v-model.number="formData.amount" 
            label="Amount"
            type="number"
            placeholder="Enter sale amount"
          />
        </div>
      </template>
      <template #footer>
        <div class="flex justify-end gap-2">
          <Button @click="closeSalesModal" class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg">
            Cancel
          </Button>
          <Button @click="saveSale" class="px-4 py-2 bg-[#10b981] hover:bg-[#059669] text-white rounded-lg">
            Record Sale
          </Button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from '@/lib/axios'
import DataTable from '@/components/DataTables.vue'
import Modal from '@/components/Modal.vue'
import TextInput from '@/components/TextInput.vue'
import SelectInput from '@/components/SelectInput.vue'
import Button from '@/components/Button.vue'

const columns = [
  { key: 'transaction_id', label: 'Transaction ID', cellClass: 'text-[#2563eb] font-semibold' },
  { key: 'product_name', label: 'Product' },
  { 
    key: 'quantity', 
    label: 'Quantity',
    cellClass: 'text-gray-900 font-semibold'
  },
  { 
    key: 'amount', 
    label: 'Amount',
    format: (value) => `KES ${formatNumber(value)}`,
    cellClass: 'text-[#10b981] font-bold'
  },
  { 
    key: 'transaction_date', 
    label: 'Date',
    format: (value) => formatDate(value),
    cellClass: 'text-gray-600'
  },
  { key: 'action', label: 'Action' }
]

const transactionsData = ref([])
const products = ref([])
const stores = ref([])
const showSalesModal = ref(false)
const formData = ref({ product_id: '', store_id: '', quantity: '', amount: '' })

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-US').format(num)
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', { 
    month: 'short', 
    day: 'numeric',
    year: 'numeric'
  })
}

const formatFullDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

const handleView = (item) => {
  console.log('Viewing transaction:', item)
}

const openSalesModal = () => {
  formData.value = { product_id: '', store_id: '', quantity: '', amount: '' }
  showSalesModal.value = true
}

const closeSalesModal = () => {
  showSalesModal.value = false
  formData.value = { product_id: '', store_id: '', quantity: '', amount: '' }
}

const saveSale = async () => {
  try {
    await api.post('/sales', {
      product_id: formData.value.product_id,
      store_id: formData.value.store_id,
      quantity: formData.value.quantity,
      amount: formData.value.amount
    })
    closeSalesModal()
    fetchTransactionsData()
  } catch (error) {
    console.error('Error creating sale:', error)
  }
}

const fetchTransactionsData = async () => {
  try {
    const response = await api.get('/transactions/sales')
    transactionsData.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching transactions:', error)
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

const fetchStores = async () => {
  try {
    const response = await api.get('/stores')
    stores.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching stores:', error)
  }
}

onMounted(() => {
  fetchTransactionsData()
  fetchProducts()
  fetchStores()
})
</script>