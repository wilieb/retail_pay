import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Login from '@/pages/Login.vue'

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
    component: () => import('@/pages/Dashboard.vue'),
    meta: { requiresAuth: true } 
  },
  { 
    path: '/inventories',
    name: 'inventories',
    component: () => import('@/pages/Inventory.vue'),
    meta: { requiresAuth: true }
  },
  { 
    path: '/products',
    name: 'products',
    component: () => import('@/pages/Products.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/sales',
    name: 'sales',
    component: () => import('@/pages/Sales.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/branches',
    name: 'branches',
    component: () => import('@/pages/Branches.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/stores',
    name: 'stores',
    component: () => import('@/pages/Stores.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/transfers',
    name: 'transfers',
    component: () => import('@/pages/Transfers.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/users',
    name: 'users',
    component: () => import('@/pages/Users.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/permissions',
    name: 'permissions',
    component: () => import('@/pages/PermissionManagement.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/branch-inventory',
    name: 'branch-inventory',
    component: () => import('@/pages/BranchInventory.vue'),
    meta: { requiresAuth: true }
  },
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

