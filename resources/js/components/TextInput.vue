<template>
  <div class="space-y-4">
    <div v-if="label" class="flex items-center justify-between">
      <label class="block text-sm font-medium text-gray-700">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </label>
      <span v-if="hint" class="text-xs text-gray-500">{{ hint }}</span>
    </div>

    <input 
      v-bind="{ ...$attrs, value: modelValue }"
      :type="type"
      :placeholder="placeholder"
      :class="inputClasses"
      :disabled="disabled"
      @input="$emit('update:modelValue', $event.target.value)"
    />

    <p v-if="error" class="text-sm text-red-500">{{ error }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  label: String,
  type: {
    type: String,
    default: 'text'
  },
  placeholder: String,
  hint: String,
  error: String,
  disabled: Boolean,
  required: Boolean
})

defineEmits(['update:modelValue'])

const inputClasses = computed(() => {
  const baseClasses = 'w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 transition-all'
  const borderClasses = props.error 
    ? 'border-red-300 focus:ring-red-200 focus:border-red-500'
    : 'border-gray-200 focus:ring-[#2563eb]/20 focus:border-[#2563eb]'
  const disabledClass = props.disabled ? 'bg-gray-50 cursor-not-allowed opacity-60' : 'bg-white'
  
  return `${baseClasses} ${borderClasses} ${disabledClass}`
})
</script>
