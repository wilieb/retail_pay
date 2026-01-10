<template>
  <div class="p-6">
    <DataTable
      title="Branches"
      subtitle="Manage organization branches"
      :columns="columns"
      :data="branchesData"
      drawer-title="Branch Details"
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </div>
            <div class="flex-1">
              <h4 class="text-xl font-bold text-gray-900 mb-1">{{ item.name }}</h4>
              <p class="text-sm text-gray-500">Branch ID: {{ item.id }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-xl">
              <p class="text-sm text-gray-500 mb-1">Total Stores</p>
              <p class="text-2xl font-bold text-gray-900">{{ item.stores?.length || 0 }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl">
              <p class="text-sm text-gray-500 mb-1">Total Staff</p>
              <p class="text-2xl font-bold text-gray-900">{{ item.users?.length || 0 }}</p>
            </div>
          </div>

          <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <span class="text-sm font-medium text-gray-600">Phone</span>
              <span class="text-sm font-semibold text-gray-900">{{ item.phone || 'N/A' }}</span>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <span class="text-sm font-medium text-gray-600">Email</span>
              <span class="text-sm font-semibold text-gray-900">{{ item.email || 'N/A' }}</span>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
              <span class="text-sm font-medium text-gray-600">Manager</span>
              <span class="text-sm font-semibold text-gray-900">{{ item.manager_name || 'Unassigned' }}</span>
            </div>
          </div>

          <div v-if="item.stores?.length" class="pt-4 border-t border-gray-100">
            <p class="text-sm font-semibold text-gray-900 mb-3">Associated Stores</p>
            <div class="space-y-2">
              <div 
                v-for="store in item.stores"
                :key="store.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <span class="text-sm font-medium text-gray-900">{{ store.name }}</span>
                <span class="text-xs text-gray-500">{{ store.id }}</span>
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

    <Modal v-model="showDeleteModal" title="Delete Branch" :closeable="!loadingDelete">
      <template #content>
        <div class="space-y-4">
          <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800 font-medium">Are you sure you want to delete this branch?</p>
          </div>
          <div class="p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600 mb-1">Branch Name</p>
            <p class="text-lg font-bold text-gray-900">{{ branchToDelete?.name }}</p>
          </div>
          <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
              <strong>Warning:</strong> This action cannot be undone. All associated data will remain but the branch will be deleted.
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
            {{ loadingDelete ? 'Deleting...' : 'Delete Branch' }}
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import DataTable from '@/components/DataTables.vue'
import Modal from '@/components/Modal.vue'
import { api } from '@/lib/axios'

const authStore = useAuthStore()

const columns = [
  { key: 'name', label: 'Branch Name' },
  { key: 'manager_name', label: 'Manager', cellClass: 'text-gray-700' },
  { key: 'phone', label: 'Phone', cellClass: 'text-gray-700' },
  { key: 'email', label: 'Email', cellClass: 'text-gray-700' },
  { key: 'store_count', label: 'Stores', cellClass: 'text-center font-semibold' },
  { key: 'action', label: 'Actions', cellClass: 'text-center' }
]

const branchesData = ref([])
const showDeleteModal = ref(false)
const branchToDelete = ref(null)
const loadingDelete = ref(false)

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const handleView = (item) => {
  // Drawer will be handled by DataTable component
}

const fetchBranches = async () => {
  try {
    const response = await api.get('/branches')
    const data = response.data.data || response.data
    branchesData.value = data.map(branch => ({
      ...branch,
      store_count: branch.stores?.length || 0,
      manager_name: branch.manager_name || 'Unassigned',
      phone: branch.phone || 'N/A',
      email: branch.email || 'N/A'
    }))
  } catch (error) {
    console.error('Error fetching branches:', error)
  }
}

const openDeleteModal = (branch) => {
  branchToDelete.value = branch
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  if (!branchToDelete.value) return
  
  loadingDelete.value = true
  try {
    await api.delete(`/branches/${branchToDelete.value.id}`)
    showDeleteModal.value = false
    branchToDelete.value = null
    await fetchBranches()
  } catch (error) {
    console.error('Error deleting branch:', error)
  } finally {
    loadingDelete.value = false
  }
}

// Make openDeleteModal available to template
const deleteBranch = (branch) => {
  if (typeof branch === 'object') {
    openDeleteModal(branch)
  } else {
    const branchObj = branchesData.value.find(b => b.id === branch)
    if (branchObj) {
      openDeleteModal(branchObj)
    }
  }
}

onMounted(() => {
  fetchBranches()
})
</script>
