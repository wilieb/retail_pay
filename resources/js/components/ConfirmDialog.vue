<template>
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full">
      <div class="p-6">
        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" :class="iconBgClass">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" :class="iconColorClass">
            <path v-if="type === 'warning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M7 11.5a4.5 4.5 0 119 0M7 11.5a4.5 4.5 0 009 0m0 0a4.5 4.5 0 11-9 0m9 0a4.5 4.5 0 01-9 0"/>
            <path v-else-if="type === 'danger'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>

        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ title }}</h3>
        <p class="text-gray-600 mb-6">{{ message }}</p>
      </div>

      <div class="border-t border-gray-100 p-6 flex gap-3 justify-end">
        <button 
          @click="$emit('cancel')"
          class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors"
        >
          {{ cancelLabel }}
        </button>
        <button 
          @click="$emit('confirm')"
          :class="[
            'px-4 py-2 text-white rounded-lg font-medium transition-colors',
            type === 'danger' 
              ? 'bg-red-500 hover:bg-red-600' 
              : 'bg-[#2563eb] hover:bg-[#1d4ed8]'
          ]"
        >
          {{ confirmLabel }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['info', 'warning', 'danger'].includes(value)
  },
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  },
  confirmLabel: {
    type: String,
    default: 'Confirm'
  },
  cancelLabel: {
    type: String,
    default: 'Cancel'
  }
})

defineEmits(['confirm', 'cancel'])

const iconBgClass = computed(() => {
  const bgMap = {
    info: 'bg-[#2563eb]/10',
    warning: 'bg-[#fbbf24]/10',
    danger: 'bg-red-100'
  }
  return bgMap[props.type]
})

const iconColorClass = computed(() => {
  const colorMap = {
    info: 'text-[#2563eb]',
    warning: 'text-[#fbbf24]',
    danger: 'text-red-500'
  }
  return colorMap[props.type]
})
</script>
