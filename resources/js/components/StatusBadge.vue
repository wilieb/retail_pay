<template>
  <span :class="badgeClasses">
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    default: 'secondary',
    validator: (value) => ['success', 'error', 'warning', 'info', 'secondary'].includes(value)
  }
})

const badgeClasses = computed(() => {
  const baseClasses = 'inline-flex px-2.5 py-1 rounded-full text-xs font-semibold'
  
  const statusClasses = {
    success: 'bg-[#10b981]/10 text-[#10b981]',
    error: 'bg-[#ef4444]/10 text-[#ef4444]',
    warning: 'bg-[#fbbf24]/10 text-[#fbbf24]',
    info: 'bg-[#2563eb]/10 text-[#2563eb]',
    secondary: 'bg-gray-100 text-gray-700'
  }
  
  return `${baseClasses} ${statusClasses[props.status]}`
})
</script>
