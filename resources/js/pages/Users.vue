<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Staff Management</h1>
        <p class="text-gray-500 mt-1">Manage users and their roles</p>
      </div>
      <button 
        @click="showAddModal = true"
        class="flex items-center gap-2 px-6 py-2.5 bg-[#2563eb] text-white rounded-xl hover:bg-[#1d4ed8] transition-colors font-medium"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add User
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Name</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Role</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Branch</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Store</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-[#2563eb]/10 flex items-center justify-center">
                    <span class="text-sm font-bold text-[#2563eb]">{{ user.name.charAt(0) }}</span>
                  </div>
                  <span class="font-medium text-gray-900">{{ user.name }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ user.email }}</td>
              <td class="px-6 py-4">
                <StatusBadge :status="getRoleStatus(user.role?.name)">
                  {{ user.role?.name || 'N/A' }}
                </StatusBadge>
              </td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ user.branch?.name || '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ user.store?.name || '-' }}</td>
              <td class="px-6 py-4">
                <div class="flex gap-2">
                  <button 
                    @click="editUser(user)"
                    class="p-2 text-[#2563eb] hover:bg-[#2563eb]/10 rounded-lg transition-colors"
                    title="Edit"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <button 
                    @click="deleteUser(user.id)"
                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                    title="Delete"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!users.length">
              <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                No users found. Add your first user to get started.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Modal 
      v-if="showAddModal"
      :title="formData.id ? 'Edit User' : 'Add User'"
      @close="showAddModal = false"
      @submit="saveUser"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
          <input 
            v-model="formData.name"
            type="text"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
            placeholder="Enter user name"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input 
            v-model="formData.email"
            type="email"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
            placeholder="Enter email address"
          />
        </div>

        <div v-if="!formData.id">
          <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <input 
            v-model="formData.password"
            type="password"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
            placeholder="Enter password"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
          <select 
            v-model="formData.role_id"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
          >
            <option value="">Select Role</option>
            <option v-for="role in roles" :key="role.id" :value="role.id">
              {{ role.name }}
            </option>
          </select>
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

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Store</label>
          <select 
            v-model="formData.store_id"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
          >
            <option value="">Select Store (Optional)</option>
            <option v-for="store in stores" :key="store.id" :value="store.id">
              {{ store.name }}
            </option>
          </select>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Modal from '@/components/Modal.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import { api } from '@/lib/axios'

const users = ref([])
const roles = ref([])
const branches = ref([])
const stores = ref([])
const showAddModal = ref(false)
const formData = ref({ name: '', email: '', password: '', role_id: '', branch_id: '', store_id: '' })

const getRoleStatus = (roleName) => {
  const statusMap = {
    'admin': 'info',
    'branch_manager': 'warning',
    'store_manager': 'success'
  }
  return statusMap[roleName] || 'secondary'
}

const fetchUsers = async () => {
  try {
    const response = await api.get('/users')
    users.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching users:', error)
  }
}

const fetchRoles = async () => {
  try {
    const response = await api.get('/roles')
    roles.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching roles:', error)
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

const fetchStores = async () => {
  try {
    const response = await api.get('/stores')
    stores.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching stores:', error)
  }
}

const saveUser = async () => {
  try {
    if (formData.value.id) {
      await api.put(`/users/${formData.value.id}`, formData.value)
    } else {
      await api.post('/users', formData.value)
    }
    showAddModal.value = false
    formData.value = { name: '', email: '', password: '', role_id: '', branch_id: '', store_id: '' }
    fetchUsers()
  } catch (error) {
    console.error('Error saving user:', error)
  }
}

const editUser = (user) => {
  formData.value = { 
    id: user.id,
    name: user.name,
    email: user.email,
    role_id: user.role_id,
    branch_id: user.branch_id,
    store_id: user.store_id
  }
  showAddModal.value = true
}

const deleteUser = async (id) => {
  if (confirm('Are you sure you want to delete this user?')) {
    try {
      await api.delete(`/users/${id}`)
      fetchUsers()
    } catch (error) {
      console.error('Error deleting user:', error)
    }
  }
}

onMounted(() => {
  fetchUsers()
  fetchRoles()
  fetchBranches()
  fetchStores()
})
</script>
