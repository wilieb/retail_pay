<template>
  <button :class="buttonClasses" @click="$emit('click')">
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'danger', 'success', 'outline'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  disabled: {
    type: Boolean,
    default: false
  },
  full: {
    type: Boolean,
    default: false
  }
})

defineEmits(['click'])

const buttonClasses = computed(() => {
  const baseClasses = 'font-medium rounded-lg transition-colors flex items-center justify-center gap-2'
  
  const sizeClasses = {
    sm: 'px-3 py-1.5 text-sm',
    md: 'px-4 py-2 text-base',
    lg: 'px-6 py-3 text-lg'
  }
  
  const variantClasses = {
    primary: 'bg-[#2563eb] text-white hover:bg-[#1d4ed8] disabled:bg-gray-300',
    secondary: 'bg-gray-100 text-gray-700 hover:bg-gray-200 disabled:bg-gray-200',
    danger: 'bg-red-500 text-white hover:bg-red-600 disabled:bg-red-300',
    success: 'bg-[#10b981] text-white hover:bg-[#059669] disabled:bg-green-300',
    outline: 'border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:bg-gray-100'
  }
  
  const widthClass = props.full ? 'w-full' : ''
  const disabledClass = props.disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'
  
  return `${baseClasses} ${sizeClasses[props.size]} ${variantClasses[props.variant]} ${widthClass} ${disabledClass}`
})
</script>
