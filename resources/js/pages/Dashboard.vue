<template>
  <div class="p-8">
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="text-gray-500 text-sm">Total Users</div>
        <div class="text-3xl font-bold mt-2">0</div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="text-gray-500 text-sm">Total Sales</div>
        <div class="text-3xl font-bold mt-2">$0</div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="text-gray-500 text-sm">Total Products</div>
        <div class="text-3xl font-bold mt-2">0</div>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <div class="text-gray-500 text-sm">Active Stores</div>
        <div class="text-3xl font-bold mt-2">0</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

onMounted(() => {
  console.log('Dashboard loaded for user:', authStore.user?.name)
})
</script>
