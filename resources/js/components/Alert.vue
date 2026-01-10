<template>
  <div class="p-4 rounded-lg border-l-4" :class="alertClasses">
    <div class="flex items-start gap-3">
      <div class="flex-shrink-0 mt-0.5">
        <component :is="iconComponent" class="w-5 h-5" />
      </div>
      <div class="flex-1">
        <p class="font-semibold" :class="textClasses">{{ title }}</p>
        <p v-if="message" class="text-sm mt-1" :class="textClasses">{{ message }}</p>
        <div v-if="$slots.default" class="mt-2">
          <slot />
        </div>
      </div>
      <button 
        v-if="closeable"
        @click="$emit('close')"
        class="flex-shrink-0"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  title: {
    type: String,
    required: true
  },
  message: String,
  closeable: {
    type: Boolean,
    default: true
  }
})

defineEmits(['close'])

const alertClasses = computed(() => {
  const classMap = {
    success: 'bg-[#10b981]/10 border-[#10b981] text-[#10b981]',
    error: 'bg-[#ef4444]/10 border-[#ef4444] text-[#ef4444]',
    warning: 'bg-[#fbbf24]/10 border-[#fbbf24] text-[#fbbf24]',
    info: 'bg-[#2563eb]/10 border-[#2563eb] text-[#2563eb]'
  }
  return classMap[props.type]
})

const textClasses = computed(() => {
  const classMap = {
    success: 'text-[#10b981]',
    error: 'text-[#ef4444]',
    warning: 'text-[#fbbf24]',
    info: 'text-[#2563eb]'
  }
  return classMap[props.type]
})

const iconComponent = computed(() => {
  const icons = {
    success: 'CheckCircleIcon',
    error: 'ExclamationIcon',
    warning: 'WarningIcon',
    info: 'InfoIcon'
  }
  return defineAsyncComponent(() => 
    import(`./icons/${icons[props.type]}.vue`).catch(() => null)
  )
})
</script>
