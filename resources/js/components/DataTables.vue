<template>
  <div class="bg-white rounded-2xl shadow-xs border-sm border-gray-100">
    <div class="p-6 border-b border-gray-100">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">{{ title }}</h2>
          <p class="text-sm text-gray-500 mt-1">{{ subtitle }}</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3">
          <div class="relative">
            <svg class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Search..." 
              class="pl-11 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb] w-full sm:w-64 text-sm"
            />
          </div>

          <select 
            v-if="showBranchFilter"
            v-model="selectedBranch"
            class="px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb] text-sm"
          >
            <option value="">All Branches</option>
            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
              {{ branch.name }}
            </option>
          </select>

          <select 
            v-if="showStoreFilter"
            v-model="selectedStore"
            class="px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb] text-sm"
          >
            <option value="">All Stores</option>
            <option v-for="store in filteredStores" :key="store.id" :value="store.id">
              {{ store.name }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <div class="overflow-x-auto p-6">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-100">
          <tr>
            <th 
              v-for="column in columns" 
              :key="column.key"
              class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
            >
              {{ column.label }}
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr 
            v-for="item in paginatedData" 
            :key="item.id"
            class="hover:bg-gray-50 transition-colors"
          >
            <td 
              v-for="column in columns" 
              :key="column.key"
              class="px-6 py-4 text-sm"
            >
              <template v-if="column.key === 'action'">
                <button 
                  @click="handleView(item)"
                  class="p-2 text-[#2563eb] hover:bg-[#2563eb]/10 rounded-lg transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </button>
              </template>
              <template v-else>
                <span :class="getCellClass(column, item)">
                  {{ formatValue(item, column) }}
                </span>
              </template>
            </td>
          </tr>
          <tr v-if="paginatedData.length === 0">
            <td :colspan="columns.length" class="px-6 py-12 text-center text-gray-500">
              No data available
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="totalPages > 1" class="p-6 border-t border-gray-100 flex items-center justify-between">
      <div class="text-sm text-gray-600">
        Showing {{ startIndex + 1 }} to {{ endIndex }} of {{ filteredData.length }} entries
      </div>
      <div class="flex gap-2">
        <button 
          @click="currentPage--"
          :disabled="currentPage === 1"
          class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          Previous
        </button>
        <button 
          v-for="page in displayedPages" 
          :key="page"
          @click="currentPage = page"
          :class="[
            'px-4 py-2 border rounded-lg text-sm font-medium transition-colors',
            currentPage === page 
              ? 'bg-[#2563eb] text-white border-[#2563eb]' 
              : 'border-gray-200 text-gray-700 hover:bg-gray-50'
          ]"
        >
          {{ page }}
        </button>
        <button 
          @click="currentPage++"
          :disabled="currentPage === totalPages"
          class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          Next
        </button>
      </div>
    </div>

    <transition name="slide">
      <div 
        v-if="showDrawer" 
        class="fixed inset-y-0 right-0 w-full sm:w-[600px] bg-white shadow-2xl z-50 overflow-y-auto"
      >
        <div class="sticky top-0 bg-white border-b border-gray-100 p-6 flex items-center justify-between z-10">
          <h3 class="text-xl font-bold text-gray-900">{{ drawerTitle }}</h3>
          <button 
            @click="closeDrawer"
            class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <div class="p-6">
          <slot name="drawer-content" :item="selectedItem"></slot>
        </div>
      </div>
    </transition>

    <div 
      v-if="showDrawer" 
      @click="closeDrawer"
      class="fixed inset-0 bg-black/50 z-40"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useAuthStore } from '@/stores/auth';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  columns: {
    type: Array,
    required: true
  },
  data: {
    type: Array,
    required: true
  },
  branches: {
    type: Array,
    default: () => []
  },
  stores: {
    type: Array,
    default: () => []
  },
  drawerTitle: {
    type: String,
    default: 'Details'
  },
  itemsPerPage: {
    type: Number,
    default: 10
  }
})

const emit = defineEmits(['view'])

const authStore = useAuthStore()
const searchQuery = ref('')
const selectedBranch = ref('')
const selectedStore = ref('')
const currentPage = ref(1)
const showDrawer = ref(false)
const selectedItem = ref(null)

const userRole = computed(() => authStore.user?.role?.name)

const showBranchFilter = computed(() => {
  return userRole.value === 'admin' && props.branches.length > 0
})

const showStoreFilter = computed(() => {
  return (userRole.value === 'admin' || userRole.value === 'branch_manager') && props.stores.length > 0
})

const filteredStores = computed(() => {
  if (!selectedBranch.value) return props.stores
  return props.stores.filter(store => store.branch_id === selectedBranch.value)
})

const filteredData = computed(() => {
  let filtered = [...props.data]

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(item => {
      return props.columns.some(column => {
        if (column.key === 'action') return false
        const value = getNestedValue(item, column.key)
        return value && value.toString().toLowerCase().includes(query)
      })
    })
  }

  if (selectedBranch.value) {
    filtered = filtered.filter(item => item.branch_id === selectedBranch.value)
  }

  if (selectedStore.value) {
    filtered = filtered.filter(item => item.store_id === selectedStore.value)
  }

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / props.itemsPerPage))

const startIndex = computed(() => (currentPage.value - 1) * props.itemsPerPage)
const endIndex = computed(() => Math.min(startIndex.value + props.itemsPerPage, filteredData.value.length))

const paginatedData = computed(() => {
  return filteredData.value.slice(startIndex.value, endIndex.value)
})

const displayedPages = computed(() => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

watch([searchQuery, selectedBranch, selectedStore], () => {
  currentPage.value = 1
})

const getNestedValue = (obj, path) => {
  return path.split('.').reduce((value, key) => value?.[key], obj)
}

const formatValue = (item, column) => {
  const value = getNestedValue(item, column.key)
  
  if (column.format) {
    return column.format(value, item)
  }
  
  return value ?? 'N/A'
}

const getCellClass = (column, item) => {
  if (column.cellClass) {
    return typeof column.cellClass === 'function' 
      ? column.cellClass(item) 
      : column.cellClass
  }
  return 'text-gray-900 font-medium'
}

const handleView = (item) => {
  selectedItem.value = item
  showDrawer.value = true
  emit('view', item)
}

const closeDrawer = () => {
  showDrawer.value = false
  selectedItem.value = null
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap');

* {
  font-family: 'IBM Plex Sans', sans-serif;
}

.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}

.slide-enter-from {
  transform: translateX(100%);
}

.slide-leave-to {
  transform: translateX(100%);
}
</style>