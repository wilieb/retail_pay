<template>
  <div class="font-inter min-h-screen flex bg-gray-50">
    <template v-if="route.meta.layout === 'blank' || !authStore.token">
      <router-view />
    </template>

    <template v-else>
      <Sidebar />
      <div class="flex-1 flex flex-col overflow-hidden">
        <Header />
        <main class="flex-1 overflow-auto bg-gray-50 p-0">
          <router-view />
        </main>
      </div>
    </template>
  </div>
</template>

<script>
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Sidebar from './components/layout/Sidebar.vue'
import Header from './components/layout/Header.vue'

export default {
  name: 'App',
  components: {
    Sidebar,
    Header
  },
  setup() {
    const route = useRoute()
    const authStore = useAuthStore()

    return {
      route,
      authStore
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap');

* {
  font-family: 'IBM Plex Sans', sans-serif;
}
</style>