<template>
  <div class="p-6">
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Staff Management</h1>
        <p class="text-gray-500 mt-1">Manage users and their roles</p>
      </div>
      <!-- <button 
        @click="openAddModal"
        class="bg-[#2563eb] hover:bg-[#1d4ed8] text-white px-4 py-2.5 rounded-lg flex items-center gap-2 font-medium transition"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add User
      </button> -->
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
                  <span class="font-medium text-gray-900">{{ user.name }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ user.email }}</td>
              <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ user.role?.name || 'N/A' }}</td>
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
                    @click="deleteUser(user)"
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
                No users found
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Modal v-if="showDeleteModal" title="Delete User" @close="closeDeleteModal">
      <template #content>
        <div class="space-y-4">
          <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800 font-medium">Are you sure you want to delete this user?</p>
          </div>
          <div class="p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600 mb-1">User Name</p>
            <p class="text-lg font-bold text-gray-900">{{ userToDelete?.name }}</p>
          </div>
          <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-800">
              <strong>Warning:</strong> This action cannot be undone. The user will be permanently deleted.
            </p>
          </div>
        </div>
      </template>
      <template #footer>
        <div class="flex justify-end gap-3">
          <button 
            @click="closeDeleteModal"
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
            {{ loadingDelete ? 'Deleting...' : 'Delete User' }}
          </button>
        </div>
      </template>
    </Modal>

    <Modal 
      v-if="showEditModal"
      :title="isEditMode ? 'Edit User' : 'Add User'"
      @close="closeEditModal"
    >
      <template #content>
        <div class="space-y-5">
          <TextInput
            v-model="formData.name"
            label="Name"
            placeholder="Enter user name"
            :error="errors.name"
            :required="true"
            @update:modelValue="errors.name = ''"
          />

          <TextInput
            v-model="formData.email"
            label="Email"
            type="email"
            placeholder="Enter email address"
            :error="errors.email"
            :required="true"
            @update:modelValue="errors.email = ''"
          />

          <TextInput
            v-if="!isEditMode"
            v-model="formData.password"
            label="Password"
            type="password"
            placeholder="Enter password (min 8 characters)"
            :error="errors.password"
            :required="true"
            @update:modelValue="errors.password = ''"
          />

          <TextInput
            v-if="isEditMode"
            v-model="formData.password"
            label="Password"
            type="password"
            placeholder="Leave blank to keep current password"
            :error="errors.password"
            @update:modelValue="errors.password = ''"
          />

          <SelectInput
            v-model="formData.role_id"
            label="Role"
            placeholder="Select role"
            :error="errors.role_id"
            :required="true"
            :options="roles.map(r => ({ value: r.id, label: r.name }))"
            @update:modelValue="errors.role_id = ''"
          />

          <SelectInput
            v-model="formData.branch_id"
            label="Branch"
            placeholder="Select branch (optional)"
            :error="errors.branch_id"
            :options="branches.map(b => ({ value: b.id, label: b.name }))"
            @update:modelValue="errors.branch_id = ''"
          />

          <SelectInput
            v-model="formData.store_id"
            label="Store"
            placeholder="Select store (optional)"
            :error="errors.store_id"
            :options="stores.map(s => ({ value: s.id, label: s.name }))"
            @update:modelValue="errors.store_id = ''"
          />

          <div v-if="errors.general" class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r">
            <p class="text-red-700 text-sm">{{ errors.general }}</p>
          </div>
        </div>
      </template>

      <template #footer>
        <div class="flex justify-end gap-3">
          <button 
            @click="closeEditModal"
            :disabled="loadingSave"
            class="px-6 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 disabled:bg-gray-100 rounded-lg font-medium transition"
          >
            Cancel
          </button>
          <button 
            @click="saveUser"
            :disabled="!isFormValid || loadingSave"
            class="px-6 py-2.5 bg-[#2563eb] hover:bg-[#1d4ed8] disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-medium transition"
          >
            {{ loadingSave ? 'Saving...' : (isEditMode ? 'Update User' : 'Add User') }}
          </button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import Modal from '@/components/Modal.vue'
import TextInput from '@/components/TextInput.vue'
import SelectInput from '@/components/SelectInput.vue'
import { api } from '@/lib/axios'

const users = ref([])
const roles = ref([])
const branches = ref([])
const stores = ref([])
const showDeleteModal = ref(false)
const showEditModal = ref(false)
const userToDelete = ref(null)
const loadingDelete = ref(false)
const loadingSave = ref(false)

const formData = ref({
  id: null,
  name: '',
  email: '',
  password: '',
  role_id: '',
  branch_id: '',
  store_id: ''
})

const errors = ref({
  name: '',
  email: '',
  password: '',
  role_id: '',
  branch_id: '',
  store_id: '',
  general: ''
})

const isEditMode = computed(() => !!formData.value.id)

const isFormValid = computed(() => {
  const hasName = formData.value.name.trim() !== ''
  const hasEmail = formData.value.email.trim() !== ''
  const hasRole = formData.value.role_id !== ''
  
  if (isEditMode.value) {
    return hasName && hasEmail && hasRole
  }
  
  const hasPassword = formData.value.password.trim() !== '' && formData.value.password.length >= 8
  return hasName && hasEmail && hasPassword && hasRole
})

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

const openAddModal = () => {
  formData.value = {
    id: null,
    name: '',
    email: '',
    password: '',
    role_id: '',
    branch_id: '',
    store_id: ''
  }
  errors.value = {
    name: '',
    email: '',
    password: '',
    role_id: '',
    branch_id: '',
    store_id: '',
    general: ''
  }
  showEditModal.value = true
}

const editUser = (user) => {
  formData.value = {
    id: user.id,
    name: user.name,
    email: user.email,
    password: '',
    role_id: user.role_id || '',
    branch_id: user.branch_id || '',
    store_id: user.store_id || ''
  }
  errors.value = {
    name: '',
    email: '',
    password: '',
    role_id: '',
    branch_id: '',
    store_id: '',
    general: ''
  }
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
  formData.value = {
    id: null,
    name: '',
    email: '',
    password: '',
    role_id: '',
    branch_id: '',
    store_id: ''
  }
  errors.value = {
    name: '',
    email: '',
    password: '',
    role_id: '',
    branch_id: '',
    store_id: '',
    general: ''
  }
}

const validateForm = () => {
  errors.value = {
    name: '',
    email: '',
    password: '',
    role_id: '',
    branch_id: '',
    store_id: '',
    general: ''
  }

  if (!formData.value.name.trim()) {
    errors.value.name = 'Name is required'
  }

  if (!formData.value.email.trim()) {
    errors.value.email = 'Email is required'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email)) {
    errors.value.email = 'Please enter a valid email'
  }

  if (!isEditMode.value && !formData.value.password.trim()) {
    errors.value.password = 'Password is required'
  } else if (!isEditMode.value && formData.value.password.length < 8) {
    errors.value.password = 'Password must be at least 8 characters'
  } else if (isEditMode.value && formData.value.password.trim() && formData.value.password.length < 8) {
    errors.value.password = 'Password must be at least 8 characters'
  }

  if (!formData.value.role_id) {
    errors.value.role_id = 'Role is required'
  }

  return Object.values(errors.value).every(error => error === '')
}

const saveUser = async () => {
  if (!validateForm()) return

  loadingSave.value = true
  try {
    const payload = {
      name: formData.value.name,
      email: formData.value.email,
      role_id: parseInt(formData.value.role_id)
    }

    if (formData.value.password.trim()) {
      payload.password = formData.value.password
    }

    if (formData.value.branch_id) {
      payload.branch_id = parseInt(formData.value.branch_id)
    }

    if (formData.value.store_id) {
      payload.store_id = parseInt(formData.value.store_id)
    }

    if (isEditMode.value) {
      await api.put(`/users/${formData.value.id}`, payload)
    } else {
      await api.post('/users', payload)
    }

    closeEditModal()
    await fetchUsers()
  } catch (error) {
    errors.value.general = error.response?.data?.message || 'Error saving user'
  } finally {
    loadingSave.value = false
  }
}

const openDeleteModal = (user) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  userToDelete.value = null
  showDeleteModal.value = false
}

const confirmDelete = async () => {
  if (!userToDelete.value) return
  
  loadingDelete.value = true
  try {
    await api.delete(`/users/${userToDelete.value.id}`)
    closeDeleteModal()
    await fetchUsers()
  } catch (error) {
    console.error('Error deleting user:', error)
  } finally {
    loadingDelete.value = false
  }
}

const deleteUser = (user) => {
  openDeleteModal(user)
}

onMounted(() => {
  fetchUsers()
  fetchRoles()
  fetchBranches()
  fetchStores()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap');

* {
  font-family: 'IBM Plex Sans', sans-serif;
}
</style>