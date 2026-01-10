<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Branches</h1>
        <p class="text-gray-500 mt-1">Manage organization branches</p>
      </div>
      <button 
        @click="showAddModal = true"
        class="flex items-center gap-2 px-6 py-2.5 bg-[#2563eb] text-white rounded-xl hover:bg-[#1d4ed8] transition-colors font-medium"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Branch
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="branch in branches" 
        :key="branch.id"
        class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 overflow-hidden group"
      >
        <div class="p-6">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h3 class="text-lg font-bold text-gray-900">{{ branch.name }}</h3>
              <p class="text-sm text-gray-500 mt-1">ID: {{ branch.id }}</p>
            </div>
            <div class="w-10 h-10 bg-[#2563eb]/10 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
              <svg class="w-5 h-5 text-[#2563eb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </div>
          </div>

          <div class="space-y-3 mb-6">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <span class="text-sm text-gray-600">Stores</span>
              <span class="font-bold text-gray-900">{{ branch.stores?.length || 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <span class="text-sm text-gray-600">Users</span>
              <span class="font-bold text-gray-900">{{ branch.users?.length || 0 }}</span>
            </div>
          </div>

          <div class="flex gap-2">
            <button 
              @click="editBranch(branch)"
              class="flex-1 px-4 py-2 bg-[#2563eb]/10 text-[#2563eb] rounded-lg hover:bg-[#2563eb]/20 transition-colors font-medium text-sm"
            >
              Edit
            </button>
            <button 
              @click="viewDetails(branch)"
              class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm"
            >
              View
            </button>
            <button 
              @click="deleteBranch(branch.id)"
              class="flex-1 px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors font-medium text-sm"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <Modal 
      v-if="showAddModal"
      :title="formData.id ? 'Edit Branch' : 'Add Branch'"
      @close="showAddModal = false"
      @submit="saveBranch"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Branch Name</label>
          <input 
            v-model="formData.name"
            type="text"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
            placeholder="Enter branch name"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
          <input 
            v-model="formData.location"
            type="text"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
            placeholder="Enter location"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
          <input 
            v-model="formData.phone"
            type="text"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
            placeholder="Enter phone"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input 
            v-model="formData.email"
            type="email"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
            placeholder="Enter email"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Manager Name</label>
          <input 
            v-model="formData.manager_name"
            type="text"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
            placeholder="Enter manager name"
          />
        </div>
      </div>
    </Modal>

    <Modal 
      v-if="showDetailsModal"
      title="Branch Details"
      @close="showDetailsModal = false"
    >
      <div class="space-y-6">
        <div class="p-4 bg-gray-50 rounded-lg">
          <p class="text-sm text-gray-500 mb-1">Branch Name</p>
          <p class="text-lg font-bold text-gray-900">{{ selectedBranch?.name }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
            <p class="text-sm text-gray-500 mb-1">Total Stores</p>
            <p class="text-2xl font-bold text-gray-900">{{ selectedBranch?.stores?.length || 0 }}</p>
          </div>
          <div class="p-4 bg-purple-50 rounded-lg border border-purple-100">
            <p class="text-sm text-gray-500 mb-1">Total Users</p>
            <p class="text-2xl font-bold text-gray-900">{{ selectedBranch?.users?.length || 0 }}</p>
          </div>
        </div>

        <div v-if="selectedBranch?.stores?.length">
          <h4 class="font-bold text-gray-900 mb-3">Stores in this Branch</h4>
          <div class="space-y-2">
            <div 
              v-for="store in selectedBranch.stores"
              :key="store.id"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
            >
              <span class="text-sm font-medium text-gray-900">{{ store.name }}</span>
              <span class="text-xs text-gray-500">ID: {{ store.id }}</span>
            </div>
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Modal from '@/components/Modal.vue'
import { api } from '@/lib/axios'

const branches = ref([])
const showAddModal = ref(false)
const showDetailsModal = ref(false)
const selectedBranch = ref(null)
const formData = ref({ id: null, name: '', location: '', phone: '', email: '', manager_name: '' })

const fetchBranches = async () => {
  try {
    const response = await api.get('/branches')
    branches.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching branches:', error)
  }
}

const saveBranch = async () => {
  try {
    if (formData.value.id) {
      await api.put(`/branches/${formData.value.id}`, {
        name: formData.value.name,
        location: formData.value.location,
        phone: formData.value.phone,
        email: formData.value.email,
        manager_name: formData.value.manager_name
      })
    } else {
      await api.post('/branches', {
        name: formData.value.name,
        location: formData.value.location,
        phone: formData.value.phone,
        email: formData.value.email,
        manager_name: formData.value.manager_name
      })
    }
    showAddModal.value = false
    formData.value = { id: null, name: '', location: '', phone: '', email: '', manager_name: '' }
    fetchBranches()
  } catch (error) {
    console.error('Error saving branch:', error)
  }
}

const editBranch = (branch) => {
  formData.value = { ...branch }
  showAddModal.value = true
}

const deleteBranch = async (id) => {
  if (confirm('Are you sure you want to delete this branch?')) {
    try {
      await api.delete(`/branches/${id}`)
      fetchBranches()
    } catch (error) {
      console.error('Error deleting branch:', error)
    }
  }
}

const viewDetails = (branch) => {
  selectedBranch.value = branch
  showDetailsModal.value = true
}

onMounted(fetchBranches)
</script>
