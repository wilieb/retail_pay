<template>
  <div class="p-6">
    <DataTable
      title="Stores"
      subtitle="Manage store locations and inventory"
      :columns="columns"
      :data="storesData"
      drawer-title="Store Details"
      @view="handleView"
    >
      <template #action="{ item }">
        <div class="flex items-center gap-2 justify-center">
          <button 
            @click.stop="handleView(item)"
            class="px-3 py-1.5 text-sm bg-[#2563eb]/10 text-[#2563eb] rounded-lg hover:bg-[#2563eb]/20 transition-colors font-medium"
          >
            View
          </button>
          <button 
            @click.stop="openDeleteModal(item)"
            class="px-3 py-1.5 text-sm bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors font-medium"
          >
            Delete
          </button>
        </div>
      </template>
      <template #drawer-content="{ item }">
        <div class="space-y-6">
          <div class="flex items-start gap-4 p-5 bg-gradient-to-br from-[#2563eb]/5 to-blue-50 rounded-xl border border-[#2563eb]/10">
            <div class="w-14 h-14 bg-[#2563eb] rounded-xl flex items-center justify-center shadow-md flex-shrink-0">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="text-xl font-bold text-gray-900 mb-1">{{ item.name }}</h4>
              <p class="text-sm text-gray-500">Store ID: {{ item.id }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-xl">
              <p class="text-sm text-gray-500 mb-1">Branch</p>
              <p class="text-lg font-bold text-gray-900">{{ item.branch_name || 'N/A' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
              <p class="text-sm text-gray-500 mb-1">Products</p>
              <p class="text-lg font-bold text-gray-900">{{ item.product_count }}</p>
            </div>
          </div>

          <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <span class="text-sm font-medium text-gray-600">Store Manager</span>
              <span class="text-sm font-semibold text-gray-900">{{ item.manager_name || 'Unassigned' }}</span>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <span class="text-sm font-medium text-gray-600">Total Staff</span>
              <span class="text-sm font-semibold text-gray-900">{{ item.staff_count }}</span>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <span class="text-sm font-medium text-gray-600">Total Inventory Value</span>
              <span class="text-sm font-bold text-[#10b981]">KES {{ formatNumber(item.inventory_value) }}</span>
            </div>
          </div>

          <div class="pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold mb-3">Inventory</p>
            <div class="space-y-2 max-h-48 overflow-y-auto">
              <div v-if="item.products?.length === 0" class="text-sm text-gray-500 text-center py-4">
                No products in this store
              </div>
              <div 
                v-for="product in item.products?.slice(0, 10)"
                :key="product.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg text-sm"
              >
                <span class="font-medium text-gray-900">{{ product.name }}</span>
                <span class="text-gray-600">{{ product.quantity }} units</span>
              </div>
              <div v-if="item.products?.length > 10" class="text-xs text-gray-500 text-center py-2">
                +{{ item.products.length - 10 }} more products
              </div>
            </div>
          </div>

          <div class="pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-400 mb-2">Last Updated</p>
            <p class="text-sm text-gray-600">{{ formatDate(item.updated_at) }}</p>
          </div>
        </div>
      </template>
    </DataTable>

    <Modal v-model="showDeleteModal" title="Delete Store" :closeable="!loadingDelete">
      <template #content>
        <div class="space-y-4">
          <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800 font-medium">Are you sure you want to delete this store?</p>
          </div>
          <div class="p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600 mb-1">Store Name</p>
            <p class="text-lg font-bold text-gray-900">{{ storeToDelete?.name }}</p>
          </div>
          <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
              <strong>Warning:</strong> This action cannot be undone. All associated data will be retained but the store will be deleted.
            </p>
          </div>
        </div>
      </template>
      <template #footer>
        <div class="flex justify-end gap-3">
          <button 
            @click="showDeleteModal = false"
            :disabled="loadingDelete"
            class="px-6 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 disabled:bg-gray-100 rounded-lg font-medium transition"
          >
            Cancel
          </button>
          <button 
            @click="confirmDelete"
            :disabled="loadingDelete"
            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white rounded-lg font-medium transition"
          >
            {{ loadingDelete ? 'Deleting...' : 'Delete Store' }}
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import DataTable from '@/components/DataTables.vue';
import Modal from '@/components/Modal.vue';
import { api } from '@/lib/axios';

const authStore = useAuthStore();

const columns = [
  { key: 'name', label: 'Store Name' },
  { key: 'branch_name', label: 'Branch' },
  { key: 'manager_name', label: 'Manager', cellClass: 'text-gray-700' },
  { key: 'product_count', label: 'Products', cellClass: 'text-center font-semibold' },
  { key: 'staff_count', label: 'Staff', cellClass: 'text-center' },
  { key: 'action', label: 'Actions', cellClass: 'text-center' }
];

const storesData = ref([]);
const showDeleteModal = ref(false);
const storeToDelete = ref(null);
const loadingDelete = ref(false);

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-US').format(num);
};

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
};

const handleView = (item) => {
} 

const fetchStores = async () => {
  try {
    const response = await api.get('/stores')
    const data = response.data.data || response.data
    storesData.value = data.map(store => ({
      ...store,
      branch_name: store.branch?.name || 'N/A',
      manager_name: store.manager_name || 'Unassigned',
      product_count: store.products?.length || 0,
      staff_count: store.users?.length || 0,
      inventory_value: (store.products || []).reduce((sum, p) => sum + (p.quantity * p.unit_price || 0), 0)
    }));
  } catch (error) {
    console.error('Error fetching stores:', error);
  }
}

const openDeleteModal = (store) => {
  storeToDelete.value = store;
  showDeleteModal.value = true;
}

const confirmDelete = async () => {
  if (!storeToDelete.value) return;
  
  loadingDelete.value = true;
  try {
    await api.delete(`/stores/${storeToDelete.value.id}`);
    showDeleteModal.value = false;
    storeToDelete.value = null;
    await fetchStores();
  } catch (error) {
    console.error('Error deleting store:', error);
  } finally {
    loadingDelete.value = false;
  }
}

const deleteStore = (store) => {
  if (typeof store === 'object') {
    openDeleteModal(store)
  } else {
    const storeObj = storesData.value.find(s => s.id === store)
    if (storeObj) {
      openDeleteModal(storeObj)
    }
  }
}

onMounted(() => {
  fetchStores()
})
</script>
