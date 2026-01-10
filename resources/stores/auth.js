import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '@/lib/axios'

const setCookie = (name, value, days = 7) => {
  const date = new Date()
  date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000)
  const expires = 'expires=' + date.toUTCString()
  document.cookie = name + '=' + value + ';' + expires + ';path=/'
}

const getCookie = (name) => {
  const nameEQ = name + '='
  const cookies = document.cookie.split(';')
  for (let cookie of cookies) {
    cookie = cookie.trim()
    if (cookie.indexOf(nameEQ) === 0) {
      return cookie.substring(nameEQ.length)
    }
  }
  return null
}

const deleteCookie = (name) => {
  document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/'
}

export const useAuthStore = defineStore('auth', () => {
  const token = ref(getCookie('auth_token') || null)
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))

  const setToken = (newToken) => {
    token.value = newToken
    if (newToken) {
      setCookie('auth_token', newToken, 7)
    } else {
      deleteCookie('auth_token')
    }
  }

  const setUser = (newUser) => {
    user.value = newUser
    if (newUser) {
      localStorage.setItem('user', JSON.stringify(newUser))
    } else {
      localStorage.removeItem('user')
    }
  }

  const login = async (email, password) => {
    const response = await api.post('/login', { email, password })
    setToken(response.data.token)
    setUser(response.data.user)
    return response.data
  }

  const register = async (name, email, password) => {
    const response = await api.post('/register', {
      name,
      email,
      password,
      password_confirmation: password,
    })
    setToken(response.data.token)
    setUser(response.data.user)
    return response.data
  }

  const logout = async () => {
    try {
      await api.post('/logout')
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      setToken(null)
      setUser(null)
    }
  }

  const isAuthenticated = () => !!token.value

  const getUserRole = () => {
    if (!user.value || !user.value.role_id) return null
    const roleMap = {
      1: 'admin',
      2: 'branch_manager',
      3: 'store_manager'
    }
    return roleMap[user.value.role_id] || null
  }

  return {
    token,
    user,
    setToken,
    setUser,
    login,
    register,
    logout,
    isAuthenticated,
    getUserRole,
  }
})
