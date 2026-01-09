import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Login from '@/js/pages/Login.vue'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { layout: 'blank' }
  },
  {
    path: '/',
    name: 'dashboard',
    component: () => import('@/js/pages/Dashboard.vue'),
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated()) {
    next({ name: 'login' })
  } else if (to.path === '/login' && authStore.isAuthenticated()) {
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router

