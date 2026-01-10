import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { api } from '@/lib/axios'

export const usePermissionStore = defineStore('permission', () => {
  const roles = ref([])
  const permissions = ref([])
  const rolePermissions = ref({})
  const loading = ref(false)

  const fetchRoles = async () => {
    loading.value = true
    try {
      const response = await api.get('/roles')
      roles.value = response.data.data || response.data
    } catch (error) {
      console.error('Error fetching roles:', error)
    } finally {
      loading.value = false
    }
  }

  const fetchPermissions = async () => {
    loading.value = true
    try {
      const response = await api.get('/permissions')
      permissions.value = response.data.data || response.data
    } catch (error) {
      console.error('Error fetching permissions:', error)
    } finally {
      loading.value = false
    }
  }

  const fetchRolePermissions = async (roleId) => {
    try {
      const response = await api.get(`/roles/${roleId}/permissions`)
      rolePermissions.value[roleId] = response.data.data || response.data
      return rolePermissions.value[roleId]
    } catch (error) {
      console.error('Error fetching role permissions:', error)
    }
  }

  const assignPermissionToRole = async (roleId, permissionId) => {
    try {
      await api.post(`/roles/${roleId}/permissions`, { permission_id: permissionId })
      await fetchRolePermissions(roleId)
    } catch (error) {
      console.error('Error assigning permission:', error)
    }
  }

  const removePermissionFromRole = async (roleId, permissionId) => {
    try {
      await api.delete(`/roles/${roleId}/permissions/${permissionId}`)
      await fetchRolePermissions(roleId)
    } catch (error) {
      console.error('Error removing permission:', error)
    }
  }

  const createRole = async (name) => {
    try {
      const response = await api.post('/roles', { name })
      await fetchRoles()
      return response.data
    } catch (error) {
      console.error('Error creating role:', error)
      throw error
    }
  }

  const updateRole = async (roleId, name) => {
    try {
      await api.put(`/roles/${roleId}`, { name })
      await fetchRoles()
    } catch (error) {
      console.error('Error updating role:', error)
    }
  }

  const deleteRole = async (roleId) => {
    try {
      await api.delete(`/roles/${roleId}`)
      await fetchRoles()
    } catch (error) {
      console.error('Error deleting role:', error)
    }
  }

  const getRoleById = computed(() => {
    return (roleId) => roles.value.find(r => r.id === roleId)
  })

  const getRolePermissions = computed(() => {
    return (roleId) => rolePermissions.value[roleId] || []
  })

  const hasPermission = (userPermissions, requiredPermission) => {
    return userPermissions?.some(p => p.name === requiredPermission)
  }

  const hasAnyPermission = (userPermissions, permissionNames = []) => {
    return permissionNames.some(name => hasPermission(userPermissions, name))
  }

  const hasAllPermissions = (userPermissions, permissionNames = []) => {
    return permissionNames.every(name => hasPermission(userPermissions, name))
  }

  return {
    roles,
    permissions,
    rolePermissions,
    loading,
    fetchRoles,
    fetchPermissions,
    fetchRolePermissions,
    assignPermissionToRole,
    removePermissionFromRole,
    createRole,
    updateRole,
    deleteRole,
    getRoleById,
    getRolePermissions,
    hasPermission,
    hasAnyPermission,
    hasAllPermissions
  }
})
