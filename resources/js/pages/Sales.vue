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
      drawer-title="Sales Details"
      @view="handleView"
    >
      <template #drawer-content="{ item }">
        <div class="h-full bg-white flex flex-col">
          <div class="bg-gray-50 px-6 py-8 text-gray-900">
            <div class="flex items-center justify-between mb-6">
              <div>
                <p class="text-xs uppercase tracking-wider opacity-90 font-medium">Sales ID</p>
                <h1 class="text-2xl font-bold mt-1 tabular-nums">{{ item.transaction_id }}</h1>
              </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-white/20">
              <div>
                <p class="text-xs opacity-75">Date & Time</p>
                <p class="font-medium">{{ formatFullDates(item.transaction_date) }}</p>
              </div>
              <div class="text-right">
                <p class="text-xs opacity-75">Status</p>
                <span class="inline-flex items-center py-1 bg-white/20 text-gray-900 text-xs font-medium rounded mt-1">
                  Completed
                </span>
              </div>
            </div>
          </div>

          <div class="flex-1 overflow-y-auto px-1 py-6 space-y-6">
            <div>
              <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Transaction Details</h2>
              <div class="bg-gray-50 rounded-lg border border-gray-200 divide-y divide-gray-200">
                <div class="px-4 py-3 flex justify-between items-center">
                  <span class="text-sm text-gray-600">Processed By</span>
                  <span class="text-sm font-semibold text-gray-900">{{ item.user_name || 'System' }}</span>
                </div>
                <div class="px-4 py-3 flex justify-between items-center">
                  <span class="text-sm text-gray-600">Store Location</span>
                  <span class="text-sm font-semibold text-gray-900">{{ item.store_name }}</span>
                </div>
                <div v-if="item.branch_name" class="px-4 py-3 flex justify-between items-center">
                  <span class="text-sm text-gray-600">Branch</span>
                  <span class="text-sm font-semibold text-gray-900">{{ item.branch_name }}</span>
                </div>
              </div>
            </div>

            <div>
              <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Items</h2>
              <div class="bg-white rounded-lg border border-gray-200">
                <table class="w-full">
                  <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Product</th>
                      <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Qty</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr class="border-b border-gray-100 last:border-0">
                      <td class="px-4 py-3">
                        <p class="text-sm font-medium text-gray-900">{{ item.product_name }}</p>
                        <p v-if="item.sku" class="text-xs text-gray-500 mt-0.5 tabular-nums">SKU: {{ item.sku }}</p>
                      </td>
                      <td class="px-4 py-3 text-right">
                        <p class="text-sm font-semibold text-gray-900 tabular-nums">{{ item.quantity }}</p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div>
              <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3">Summary</h2>
              <div class="bg-gray-50 rounded-lg border border-gray-200">
                <div class="px-4 py-3 flex justify-between items-center">
                  <span class="text-sm text-gray-600">Subtotal</span>
                  <span class="text-sm font-medium text-gray-900 tabular-nums">KES {{ formatNumbers(item.amount) }}</span>
                </div>
                <div class="px-4 py-4 bg-gray-50 bg-opacity-5 border-t border-gray-600 border-opacity-20 flex justify-between items-center">
                  <span class="text-base font-semibold text-gray-900">Total Amount</span>
                  <span class="text-2xl font-bold text-[#2563eb] tabular-nums">KES {{ formatNumbers(item.amount) }}</span>
                </div>
              </div>
            </div>

            <!-- <div class="pt-4 border-t border-gray-200">
              <p class="text-xs text-gray-500 text-center">
                This is an automatically generated receipt.
              </p>
              <p class="text-xs text-gray-500 text-center mt-1 tabular-nums">
                Transaction ID: {{ item.transaction_id }}
              </p>
            </div> -->
          </div>

          <!-- <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <button 
              @click="printReceipt"
              class="w-full bg-[#2563eb] hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition-colors flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
              </svg>
              Print Receipt
            </button>
          </div> -->
        </div>
      </template>
    </DataTable>

    <Modal v-if="showSalesModal" @close="closeSalesModal" title="Record New Sale">
      <template #content>
        <div class="space-y-5">
          <SelectInput 
            v-if="isAdmin"
            v-model="formData.branch_id" 
            label="Branch"
            placeholder="Select branch"
            :error="errors.branch_id"
            :required="true"
            :options="branches.map(b => ({ value: b.id, label: b.name }))"
            @update:modelValue="onBranchChange"
          />

          <SelectInput 
            v-if="isAdmin || isBranchManager"
            v-model="formData.store_id" 
            label="Store"
            placeholder="Select store"
            :error="errors.store_id"
            :required="true"
            :disabled="isAdmin && !formData.branch_id"
            :options="availableStores.map(s => ({ value: s.id, label: `${s.name}${s.branch?.name ? ' - ' + s.branch.name : ''}` }))"
            @update:modelValue="onStoreChange"
          />

          <SelectInput 
            v-model="formData.product_id" 
            label="Product"
            placeholder="Select product"
            :error="errors.product_id"
            :required="true"
            :disabled="(isAdmin || isBranchManager) && !formData.store_id"
            :options="filteredProducts.map(p => ({ value: p.id, label: `${p.product_name} (SKU: ${p.sku}) - Stock: ${p.stock || 0}` }))"
            @update:modelValue="onProductChange"
          />

          <TextInput 
            v-model.number="formData.quantity" 
            label="Quantity Sold"
            type="number"
            placeholder="Enter quantity"
            :error="errors.quantity"
            :required="true"
            :min="1"
            @update:modelValue="onQuantityChange"
          />

          <TextInput 
            v-model.number="formData.amount" 
            label="Sale Amount (KES)"
            type="number"
            placeholder="Enter sale amount"
            :error="errors.amount"
            :required="true"
            :min="0"
            step="0.01"
            @update:modelValue="validateForm"
          />
          <div v-if="errors.general" class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r">
            <p class="text-red-700 text-sm">{{ errors.general }}</p>
          </div>
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
            class="px-6 py-2.5 bg-[#10b981] hover:bg-[#059669] disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-medium transition"
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
  { key: 'transaction_id', label: 'Transaction ID' },
  { key: 'product_name', label: 'Product' },
  { key: 'quantity', label: 'Quantity', cellClass: 'text-gray-900 font-semibold' },
  { key: 'amount', label: 'Amount', format: (value) => `KES ${formatNumber(value)}` },
  { key: 'transaction_date', label: 'Date', format: (value) => formatDate(value), cellClass: 'text-gray-600' },
  { key: 'action', label: 'Action' }
];

const transactionsData = ref([])
const products = ref([])
const stores = ref([])
const branches = ref([])
const showSalesModal = ref(false)
const loading = ref(false)
const formData = ref({ branch_id: '', store_id: '', product_id: '', quantity: '', amount: '' })
const errors = ref({ branch_id: '', store_id: '', product_id: '', quantity: '', amount: '' })

const userRole = computed(() => authStore.user?.role?.name)
const isAdmin = computed(() => userRole.value === 'admin')
const isBranchManager = computed(() => userRole.value === 'branch_manager')
const isStoreManager = computed(() => userRole.value === 'store_manager')

const filteredStores = computed(() => {
  if (!formData.value.branch_id) return []
  return stores.value.filter(store => store.branch_id == formData.value.branch_id)
})

const filteredProducts = computed(() => {
  if (isStoreManager.value) {
    const userStoreId = authStore.user?.store_id
    return products.value.filter(p => p.store_id === userStoreId)
  }
  
  if (isBranchManager.value) {
    if (!formData.value.store_id) return []
    return products.value.filter(p => p.store_id == formData.value.store_id)
  }
  
  if (isAdmin.value) {
    if (!formData.value.store_id) return []
    return products.value.filter(p => p.store_id == formData.value.store_id)
  }
  
  return products.value
})

const availableStores = computed(() => {
  if (isStoreManager.value) {
    const userStoreId = authStore.user?.store_id
    return stores.value.filter(s => s.id === userStoreId)
  }
  
  if (isBranchManager.value) {
    const userBranchId = authStore.user?.branch_id
    return stores.value.filter(s => s.branch_id === userBranchId)
  }

  
  return filteredStores.value
})

const isFormValid = computed(() => {
  const hasProduct = formData.value.product_id
  const hasQuantity = formData.value.quantity && formData.value.quantity > 0
  const hasAmount = formData.value.amount && formData.value.amount > 0
  
  if (isStoreManager.value) {
    return hasProduct && hasQuantity && hasAmount
  }
  
  if (isBranchManager.value) {
    return formData.value.store_id && hasProduct && hasQuantity && hasAmount
  }
  
  if (isAdmin.value) {
    return formData.value.branch_id && formData.value.store_id && hasProduct && hasQuantity && hasAmount
  }
  
  return false
})

const formatFullDates = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatNumbers = (num) => {
  if (!num) return '0.00';
  return parseFloat(num).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
};

const printReceipt = () => {
  window.print();
};

const formatNumber = (num) => new Intl.NumberFormat('en-US').format(num)
const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
const formatFullDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const onBranchChange = () => {
  formData.value.store_id = ''
  formData.value.product_id = ''
  errors.value.branch_id = ''
  validateForm()
}

const onStoreChange = () => {
  formData.value.product_id = ''
  errors.value.store_id = ''
  validateForm()
}

const onQuantityChange = () => {
  const selectedProduct = products.value.find(p => p.id == formData.value.product_id)
  if (selectedProduct && selectedProduct.retail_price && formData.value.quantity) {
    formData.value.amount = selectedProduct.retail_price * formData.value.quantity
  }
  validateForm()
}

const onProductChange = () => {
  errors.value.product_id = ''
  const selectedProduct = products.value.find(p => p.id === formData.value.product_id)
  if (selectedProduct && selectedProduct.retail_price && formData.value.quantity) {
    formData.value.amount = selectedProduct.retail_price * formData.value.quantity
  }
  validateForm()
}

const validateForm = () => {
  errors.value = {}
  
  if (isAdmin.value && !formData.value.branch_id) {
    errors.value.branch_id = 'Branch is required'
  }
  
  if ((isAdmin.value || isBranchManager.value) && !formData.value.store_id) {
    errors.value.store_id = 'Store is required'
  }
  
  if (!formData.value.product_id) {
    errors.value.product_id = 'Product is required'
  }
  
  if (!formData.value.quantity || formData.value.quantity <= 0) {
    errors.value.quantity = 'Quantity must be greater than 0'
  }
  
  if (!formData.value.amount || formData.value.amount <= 0) {
    errors.value.amount = 'Amount must be greater than 0'
  }
  
  const selectedProduct = products.value.find(p => p.id === formData.value.product_id)
  if (selectedProduct && formData.value.quantity > selectedProduct.stock) {
    errors.value.quantity = `Only ${selectedProduct.stock} units available in stock`
  }
}

const handleView = (item) => console.log('Viewing transaction:', item)

const openSalesModal = () => {
  formData.value = { branch_id: '', store_id: '', product_id: '', quantity: '', amount: '' }
  
  if (isStoreManager.value) {
    formData.value.store_id = authStore.user?.store_id || ''
  }
  
  if (isBranchManager.value) {
    formData.value.branch_id = authStore.user?.branch_id || ''
  }
  
  errors.value = {}
  showSalesModal.value = true
}

const closeSalesModal = () => {
  showSalesModal.value = false
  formData.value = { branch_id: '', store_id: '', product_id: '', quantity: '', amount: '' }
  errors.value = {}
}

const saveSale = async () => {
  validateForm()
  if (!isFormValid.value || Object.keys(errors.value).length > 0) return
  
  loading.value = true
  try {
    const payload = {
      product_id: parseInt(formData.value.product_id),
      quantity: parseInt(formData.value.quantity),
      amount: parseFloat(formData.value.amount)
    }
    
    if (isAdmin.value || isBranchManager.value) {
      payload.from_store_id = parseInt(formData.value.store_id)
    }
    
    await api.post('/sales', payload)
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
    const response = await api.get('/transactions?type=sales')
    transactionsData.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching transactions:', error)
  }
}

const fetchProducts = async () => {
  try {
    const response = await api.get('/stores')
    const storesData = response.data.data || response.data
    
    const allProducts = []
    storesData.forEach(store => {
      if (store.products && Array.isArray(store.products)) {
        store.products.forEach(product => {
          allProducts.push({
            ...product,
            product_name: product.name,
            sku: product.sku,
            stock: product.quantity,
            store_id: store.id,
            store_name: store.name,
            branch_id: store.branch_id,
            branch_name: store.branch?.name
          })
        })
      }
    })
    
    products.value = allProducts
  } catch (error) {
    console.error('Error fetching products:', error)
  }
}

const fetchStores = async () => {
  try {
    const response = await api.get('/stores')
    const storesData = response.data.data || response.data
    
    stores.value = storesData.map(store => ({
      id: store.id,
      name: store.name,
      branch_id: store.branch_id,
      branch: store.branch
    }))
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

onMounted(async () => {
  fetchTransactionsData()
  await fetchStores()
  await fetchProducts()
  if (isAdmin.value) {
    fetchBranches()
  }
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

@media print {
  button {
    display: none;
  }
}
</style>