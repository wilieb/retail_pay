<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Permissions Management</h1>
        <p class="text-gray-500 mt-1">Configure roles and permissions for your organization</p>
      </div>
      <button 
        @click="showRoleModal = true"
        class="flex items-center gap-2 px-6 py-2.5 bg-[#2563eb] text-white rounded-xl hover:bg-[#1d4ed8] transition-colors font-medium"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Role
      </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
          <div class="bg-gradient-to-br from-[#2563eb] to-blue-600 p-6 text-white">
            <h2 class="text-lg font-bold">Roles</h2>
            <p class="text-blue-100 text-sm mt-1">{{ roles.length }} roles configured</p>
          </div>

          <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
            <button 
              v-for="role in roles"
              :key="role.id"
              @click="selectRole(role)"
              :class="[
                'w-full text-left px-4 py-3 hover:bg-gray-50 transition-colors border-l-4 font-medium',
                selectedRole?.id === role.id 
                  ? 'bg-[#2563eb]/5 border-l-[#2563eb] text-[#2563eb]'
                  : 'border-l-transparent text-gray-700'
              ]"
            >
              {{ role.name }}
            </button>
          </div>
        </div>
      </div>

      <div class="lg:col-span-2">
        <div v-if="selectedRole" class="space-y-6">
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h3 class="text-2xl font-bold text-gray-900">{{ selectedRole.name }}</h3>
                <p class="text-sm text-gray-500 mt-1">Role ID: {{ selectedRole.id }}</p>
              </div>
              <div class="flex gap-2">
                <button 
                  @click="editRole(selectedRole)"
                  class="p-2 text-[#2563eb] hover:bg-[#2563eb]/10 rounded-lg transition-colors"
                  title="Edit role"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button 
                  @click="deleteRole(selectedRole)"
                  class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                  title="Delete role"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
              <p class="text-sm text-blue-700">
                Assign permissions to this role. Users with this role will have access to all assigned permissions.
              </p>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
              <h4 class="font-bold text-gray-900">Permissions</h4>
              <p class="text-sm text-gray-500 mt-1">
                {{ rolePermissions[selectedRole.id]?.length || 0 }} of {{ permissions.length }} assigned
              </p>
            </div>

            <div class="divide-y divide-gray-100">
              <div v-if="!permissions.length" class="p-6 text-center text-gray-500">
                No permissions available
              </div>

              <div 
                v-for="permission in permissions"
                :key="permission.id"
                class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between"
              >
                <div class="flex-1">
                  <p class="font-medium text-gray-900">{{ permission.name }}</p>
                  <p class="text-sm text-gray-500">{{ permission.description }}</p>
                </div>

                <button 
                  @click="togglePermission(permission.id)"
                  :class="[
                    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                    isPermissionAssigned(permission.id)
                      ? 'bg-[#10b981]'
                      : 'bg-gray-200'
                  ]"
                  :title="isPermissionAssigned(permission.id) ? 'Revoke' : 'Assign'"
                >
                  <span :class="[
                    'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                    isPermissionAssigned(permission.id) ? 'translate-x-6' : 'translate-x-1'
                  ]" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
          <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
          </svg>
          <p class="text-gray-500">Select a role to manage its permissions</p>
        </div>
      </div>
    </div>

    <Modal 
      v-if="showRoleModal"
      :title="formData.id ? 'Edit Role' : 'Create Role'"
      @close="showRoleModal = false"
      @submit="saveRole"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Role Name</label>
          <input 
            v-model="formData.name"
            type="text"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb]"
            placeholder="e.g., Manager, Supervisor, etc."
          />
        </div>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePermissionStore } from '@/stores/permission'
import Modal from '@/components/Modal.vue'
import { api } from '@/lib/axios'

const permissionStore = usePermissionStore()
const roles = ref([])
const permissions = ref([])
const selectedRole = ref(null)
const showRoleModal = ref(false)
const formData = ref({ id: null, name: '' })

const rolePermissions = computed(() => permissionStore.rolePermissions)

const isPermissionAssigned = (permissionId) => {
  if (!selectedRole.value) return false
  const assigned = rolePermissions.value[selectedRole.value.id] || []
  return assigned.some(p => p.id === permissionId)
}

const selectRole = async (role) => {
  selectedRole.value = role
  await permissionStore.fetchRolePermissions(role.id)
}

const editRole = (role) => {
  formData.value = { id: role.id, name: role.name }
  showRoleModal.value = true
}

const deleteRole = async (role) => {
  if (confirm('Are you sure you want to delete this role?')) {
    try {
      await permissionStore.deleteRole(role.id)
      if (selectedRole.value?.id === role.id) {
        selectedRole.value = null
      }
      await fetchRoles()
    } catch (error) {
      console.error('Error deleting role:', error)
    }
  }
}

const saveRole = async () => {
  try {
    if (formData.value.id) {
      await permissionStore.updateRole(formData.value.id, formData.value.name)
    } else {
      await permissionStore.createRole(formData.value.name)
    }
    showRoleModal.value = false
    formData.value = { id: null, name: '' }
    await fetchRoles()
  } catch (error) {
    console.error('Error saving role:', error)
  }
}

const togglePermission = async (permissionId) => {
  if (!selectedRole.value) return

  if (isPermissionAssigned(permissionId)) {
    await permissionStore.removePermissionFromRole(selectedRole.value.id, permissionId)
  } else {
    await permissionStore.assignPermissionToRole(selectedRole.value.id, permissionId)
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

const fetchPermissions = async () => {
  try {
    const response = await api.get('/permissions')
    permissions.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching permissions:', error)
  }
}

onMounted(() => {
  fetchRoles()
  fetchPermissions()
  permissionStore.fetchRoles()
  permissionStore.fetchPermissions()
})
</script>
