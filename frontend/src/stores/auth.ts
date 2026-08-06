import { defineStore } from 'pinia'
import { api, ensureCsrfCookie } from '../lib/api'

interface User {
  id: number
  name: string
  email: string
  role: 'registered' | 'admin'
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    loaded: false,
  }),
  getters: {
    isAuthenticated: (state) => state.user !== null,
    isAdmin: (state) => state.user?.role === 'admin',
  },
  actions: {
    async fetchUser() {
      try {
        const { data } = await api.get<User>('/auth/user')
        this.user = data
      } catch {
        this.user = null
      } finally {
        this.loaded = true
      }
    },
    async register(name: string, email: string, password: string, password_confirmation: string) {
      await ensureCsrfCookie()
      await api.post('/auth/register', { name, email, password, password_confirmation })
      await this.fetchUser()
    },
    async login(email: string, password: string) {
      await ensureCsrfCookie()
      await api.post('/auth/login', { email, password })
      await this.fetchUser()
    },
    async logout() {
      await api.post('/auth/logout')
      this.user = null
    },
  },
})
