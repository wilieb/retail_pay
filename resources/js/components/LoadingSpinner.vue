<template>
  <div class="flex items-center justify-center gap-2">
    <div class="w-2 h-2 bg-[#2563eb] rounded-full animate-bounce" style="animation-delay: 0s" />
    <div class="w-2 h-2 bg-[#2563eb] rounded-full animate-bounce" style="animation-delay: 0.15s" />
    <div class="w-2 h-2 bg-[#2563eb] rounded-full animate-bounce" style="animation-delay: 0.3s" />
    <span v-if="text" class="ml-2 text-sm text-gray-600">{{ text }}</span>
  </div>
</template>

<script setup>
defineProps({
  text: {
    type: String,
    default: 'Loading...'
  }
})
</script>

<style scoped>
@keyframes bounce {
  0%, 80%, 100% {
    opacity: 0.5;
    transform: translateY(0);
  }
  40% {
    opacity: 1;
    transform: translateY(-10px);
  }
}

.animate-bounce {
  animation: bounce 1.4s infinite;
}
</style>
