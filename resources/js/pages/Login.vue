<template>
  <div class="min-h-screen w-full flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md mx-4">
      <div class="bg-white rounded-2xl shadow-xs p-8 lg:p-10">
        <div class="mb-8 text-center">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#2563eb] to-blue-600 rounded-2xl mb-4 shadow-xs shadow-[#2563eb]/20">
            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back!</h1>
          <p class="text-gray-600">Sign in to your account</p>
        </div>

        <div v-if="errorMessage" class="mb-6 p-4 bg-[#FDEBEE] border border-[#F43F5E] rounded-lg">
          <p class="text-[#F43F5E] text-sm">{{ errorMessage }}</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5">
          <div class="relative">
            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <input 
              v-model="email"
              type="email"
              placeholder="Email Address"
              required
              class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb] transition"
            />
          </div>

          <div class="relative">
            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </div>
            <input 
              v-model="password"
              type="password"
              placeholder="Password"
              required
              class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2563eb]/20 focus:border-[#2563eb] transition"
            />
          </div>

          <button 
            type="submit"
            :disabled="loading"
            class="w-full py-3.5 bg-gradient-to-r from-[#2563eb] to-blue-600 text-white font-semibold rounded-lg hover:from-[#1d4ed8] hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition shadow-lg shadow-[#2563eb]/30 hover:shadow-xl hover:shadow-[#2563eb]/40"
          >
            {{ loading ? 'Logging in...' : 'Login' }}
          </button>
        </form>

        <div class="mt-6 text-center">
          <p class="text-gray-600 text-sm">
            Don't have an account? 
            <a href="#" class="text-[#2563eb] font-semibold hover:text-blue-700 transition">Register</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const loading = ref(false);
const errorMessage = ref('');

const handleLogin = async () => {
  errorMessage.value = '';
  loading.value = true;

  try {
    await authStore.login(email.value, password.value);
    router.push({ name: 'dashboard' });
  } catch (error) {
    console.error('Login failed:', error);
    errorMessage.value = error.response?.data?.message || 'Invalid email or password';
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap');

* {
  font-family: 'IBM Plex Sans', sans-serif;
}

button, input, a {
  transition: all 0.3s ease;
}

input:focus {
  transform: translateY(-1px);
}

button:not(:disabled):hover {
  transform: translateY(-2px);
}
</style>