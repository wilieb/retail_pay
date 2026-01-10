<template>
  <div class="p-6">
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Sales Transactions</h1>
        <p class="text-gray-500 mt-1">Record and manage sales history</p>
      </div>
      <button 
        v-if="canRecordSale"
        @click="openSalesModal()" 
        class="bg-[#2563eb] hover:bg-[#1d4ed8] text-white px-4 py-2.5 rounded-lg flex items-center gap-2 font-medium transition"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Record Sale
      </button>
    </div>

    <DataTable
      title=""
      subtitle=""
      :columns="columns"
      :data="transactionsData"
      drawer-title="Sales Receipt"
      @view="handleView"
    >
      <template #drawer-content="{ item }">
        <div class="space-y-6">
          <div class="p-5 bg-gray-50 rounded-xl border border-gray-200">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Sales Receipt</p>
            <h2 class="text-2xl font-bold text-gray-900 mt-1">#{{ item.transaction_id }}</h2>
            <p class="text-sm text-gray-600 mt-1">{{ formatFullDate(item.transaction_date) }}</p>
          </div>

          <div class="flex justify-between items-start p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl">
            <div>
              <p class="text-sm text-gray-500">Initiated By</p>
              <p class="text-lg font-semibold text-gray-900 mt-1">{{ item.user_name || 'System' }}</p>
            </div>
            <div class="text-right">
              <p class="text-sm text-gray-500">Status</p>
              <span class="inline-block mt-1 px-3 py-1 bg-[#10b981]/10 text-[#10b981] text-sm font-medium rounded-full">Completed</span>
            </div>
          </div>

          <div class="space-y-2 p-5 bg-gray-50 rounded-xl">
            <h3 class="font-semibold text-gray-900 mb-4">Product Details</h3>
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
              <p class="text-sm text-gray-600">Product</p>
              <p class="font-medium text-gray-900">{{ item.product_name }}</p>
            </div>
            <div class="flex justify-between items-center py-3 border-b border-gray-200">
              <p class="text-sm text-gray-600">Quantity</p>
              <p class="font-medium text-gray-900">{{ item.quantity }} units</p>
            </div>
            <div class="flex justify-between items-center pt-3">
              <p class="text-sm text-gray-600">From Store</p>
              <p class="font-medium text-gray-900">{{ item.store_name }}</p>
            </div>
          </div>

          <div class="p-5 bg-gradient-to-br from-[#10b981]/5 to-emerald-50 rounded-xl border border-[#10b981]/20">
            <p class="text-sm text-gray-600 uppercase tracking-wide">Total Amount</p>
            <p class="text-3xl font-bold text-[#10b981] mt-2">KES {{ formatNumber(item.amount) }}</p>
          </div>

          <div v-if="item.branch_name" class="p-4 bg-gray-50 rounded-xl border border-gray-200">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2">Location</p>
            <p class="text-gray-900">{{ item.branch_name }}</p>
          </div>
        </div>
      </template>
    </DataTable>

    <Modal v-if="showSalesModal" @close="closeSalesModal" title="Record New Sale">
      <template #content>
        <div class="space-y-5">
          <SelectInput 
            v-model="formData.product_id" 
            label="Product"
            placeholder="Select product"
            :error="errors.product_id"
            :required="true"
            :options="products.map(p => ({ value: p.id, label: `${p.product_name} (SKU: ${p.sku})` }))"
            @update:modelValue="validateForm"
          />
          <SelectInput 
            v-model="formData.store_id" 
            label="Store"
            placeholder="Select store"
            :error="errors.store_id"
            :required="true"
            :options="stores.map(s => ({ value: s.id, label: `${s.name} (${s.branch?.name || 'N/A'})` }))"
            @update:modelValue="validateForm"
          />
          <TextInput 
            v-model.number="formData.quantity" 
            label="Quantity Sold"
            type="number"
            placeholder="Enter quantity"
            :error="errors.quantity"
            :required="true"
            @update:modelValue="validateForm"
          />
          <TextInput 
            v-model.number="formData.amount" 
            label="Sale Amount (KES)"
            type="number"
            placeholder="Enter sale amount"
            :error="errors.amount"
            :required="true"
            @update:modelValue="validateForm"
          />
        </div>
      </template>

      <template #footer>
        <div class="flex justify-end gap-3">
          
          <button 
            @click="closeSalesModal" 
            class="px-6 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition"
          >
            Cancel
          </button>
          <button 
            @click="saveSale" 
            :disabled="!isFormValid || loading"
            class="px-6 py-2.5 bg-[#10b981] hover:bg-[#059669] disabled:bg-gray-300 text-white rounded-lg font-medium transition"
          >
            {{ loading ? 'Recording...' : 'Record Sale' }}
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { api } from '@/lib/axios'
import DataTable from '@/components/DataTables.vue'
import Modal from '@/components/Modal.vue'
import TextInput from '@/components/TextInput.vue'
import SelectInput from '@/components/SelectInput.vue'

const authStore = useAuthStore()
const canRecordSale = computed(() => authStore.token)

const columns = [
  { key: 'transaction_id', label: 'Transaction ID', cellClass: 'text-[#2563eb] font-semibold' },
  { key: 'product_name', label: 'Product' },
  { key: 'quantity', label: 'Quantity', cellClass: 'text-gray-900 font-semibold' },
  { key: 'amount', label: 'Amount', format: (value) => `KES ${formatNumber(value)}`, cellClass: 'text-[#10b981] font-bold' },
  { key: 'transaction_date', label: 'Date', format: (value) => formatDate(value), cellClass: 'text-gray-600' },
  { key: 'action', label: 'Action' }
]

const transactionsData = ref([])
const products = ref([])
const stores = ref([])
const showSalesModal = ref(false)
const loading = ref(false)
const formData = ref({ product_id: '', store_id: '', quantity: '', amount: '' })
const errors = ref({ product_id: '', store_id: '', quantity: '', amount: '' })

const isFormValid = computed(() => {
  return formData.value.product_id && formData.value.store_id && 
         formData.value.quantity && formData.value.quantity > 0 &&
         formData.value.amount && formData.value.amount > 0
})

const formatNumber = (num) => new Intl.NumberFormat('en-US').format(num)
const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
const formatFullDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const validateForm = () => {
  errors.value = {}
  if (!formData.value.product_id) errors.value.product_id = 'Product is required'
  if (!formData.value.store_id) errors.value.store_id = 'Store is required'
  if (!formData.value.quantity || formData.value.quantity <= 0) errors.value.quantity = 'Quantity must be > 0'
  if (!formData.value.amount || formData.value.amount <= 0) errors.value.amount = 'Amount must be > 0'
}

const handleView = (item) => console.log('Viewing transaction:', item)

const openSalesModal = () => {
  formData.value = { product_id: '', store_id: '', quantity: '', amount: '' };
  errors.value = {};
  showSalesModal.value = true;
}

const closeSalesModal = () => {
  showSalesModal.value = false;
  formData.value = { product_id: '', store_id: '', quantity: '', amount: '' };
  errors.value = {};
}

const saveSale = async () => {
  validateForm()
  if (!isFormValid.value) return
  loading.value = true
  try {
    await api.post('/sales', formData.value)
    closeSalesModal()
    await fetchTransactionsData()
  } catch (error) {
    errors.value.general = error.response?.data?.message || 'Error recording sale'
  } finally {
    loading.value = false
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
