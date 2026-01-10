<template>
  <div class="p-12 text-center">
    <svg 
      v-if="icon === 'empty'"
      class="w-16 h-16 text-gray-400 mx-auto mb-4" 
      fill="none" 
      stroke="currentColor" 
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>

    <svg 
      v-else-if="icon === 'search'"
      class="w-16 h-16 text-gray-400 mx-auto mb-4" 
      fill="none" 
      stroke="currentColor" 
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>

    <svg 
      v-else-if="icon === 'inbox'"
      class="w-16 h-16 text-gray-400 mx-auto mb-4" 
      fill="none" 
      stroke="currentColor" 
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
    </svg>

    <svg 
      v-else-if="icon === 'error'"
      class="w-16 h-16 text-red-400 mx-auto mb-4" 
      fill="none" 
      stroke="currentColor" 
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>

    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ title }}</h3>
    <p class="text-gray-500 mb-4">{{ description }}</p>

    <button 
      v-if="actionLabel"
      @click="$emit('action')"
      class="px-6 py-2 bg-[#2563eb] text-white rounded-lg hover:bg-[#1d4ed8] transition-colors font-medium"
    >
      {{ actionLabel }}
    </button>
  </div>
</template>

<script setup>
defineProps({
  icon: {
    type: String,
    default: 'empty',
    validator: (value) => ['empty', 'search', 'inbox', 'error'].includes(value)
  },
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: 'No items to display'
  },
  actionLabel: String
})

defineEmits(['action'])
</script>
