import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const api = axios.create({
    baseURL: import.meta.env.VITE_APP_API_BASE_URL || 'http://localhost:8000/api',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    },
    withCredentials: true,
})

api.interceptors.request.use((config) => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (csrfToken) {
        config.headers['X-CSRF-TOKEN'] = csrfToken
    }

    const authStore = useAuthStore()
    if (authStore.token) {
        config.headers.Authorization = `Bearer ${authStore.token}`
    }
    return config
})

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            const authStore = useAuthStore()
            if (authStore.token) {
                authStore.setToken(null)
                authStore.setUser(null)
                if (window.location.pathname !== '/login') {
                    window.location.href = '/login'
                }
            }
        }
        return Promise.reject(error)
    }
)

export default api
export { api, axios }
