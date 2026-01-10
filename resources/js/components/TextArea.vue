<template>
  <div class="space-y-4">
    <div v-if="label" class="flex items-center justify-between">
      <label class="block text-sm font-medium text-gray-700">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </label>
      <span v-if="hint" class="text-xs text-gray-500">{{ hint }}</span>
    </div>

    <textarea 
      v-bind="{ ...$attrs, value: modelValue }"
      :placeholder="placeholder"
      :rows="rows"
      :class="textareaClasses"
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
    type: String,
    default: ''
  },
  label: String,
  placeholder: String,
  hint: String,
  error: String,
  disabled: Boolean,
  required: Boolean,
  rows: {
    type: Number,
    default: 4
  }
})

defineEmits(['update:modelValue'])

const textareaClasses = computed(() => {
  const baseClasses = 'w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:ring-2 transition-all resize-none'
  const borderClasses = props.error 
    ? 'border-red-300 focus:ring-red-200 focus:border-red-500'
    : 'border-gray-200 focus:ring-[#2563eb]/20 focus:border-[#2563eb]'
  const disabledClass = props.disabled ? 'bg-gray-50 cursor-not-allowed opacity-60' : 'bg-white'
  
  return `${baseClasses} ${borderClasses} ${disabledClass}`
})
</script>
