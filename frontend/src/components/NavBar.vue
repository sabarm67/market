<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const menuOpen = ref(false)
const searchQuery = ref('')

function search() {
  if (!searchQuery.value.trim()) return
  router.push({ name: 'company-profile', params: { stockCode: searchQuery.value.trim() } })
  searchQuery.value = ''
}

async function doLogout() {
  await auth.logout()
  router.push({ name: 'dashboard' })
}
</script>

<template>
  <header class="border-b border-slate-200 dark:border-slate-800">
    <div class="mx-auto flex max-w-6xl items-center gap-4 px-4 py-3">
      <router-link to="/" class="font-semibold text-brand-600 dark:text-brand-500 shrink-0">
        Share Monitor <span class="text-xs font-normal text-slate-400">MY</span>
      </router-link>

      <nav class="hidden md:flex items-center gap-4 text-sm">
        <router-link to="/" class="hover:text-brand-600">Dashboard</router-link>
        <router-link to="/screener" class="hover:text-brand-600">Screener</router-link>
        <router-link to="/shariah" class="hover:text-brand-600">Shariah</router-link>
        <router-link v-if="auth.isAuthenticated" to="/watchlist" class="hover:text-brand-600">Watchlist</router-link>
        <router-link v-if="auth.isAuthenticated" to="/alerts" class="hover:text-brand-600">Alerts</router-link>
        <router-link v-if="auth.isAuthenticated" to="/portfolio" class="hover:text-brand-600">Portfolio</router-link>
        <router-link v-if="auth.isAdmin" to="/admin/shariah-import" class="hover:text-brand-600">Admin</router-link>
      </nav>

      <form class="ml-auto flex-1 max-w-xs" @submit.prevent="search">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search stock code..."
          class="w-full rounded border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-1.5 text-sm"
        />
      </form>

      <div class="hidden md:flex items-center gap-3 text-sm shrink-0">
        <template v-if="auth.isAuthenticated">
          <span class="text-slate-500">{{ auth.user?.email }}</span>
          <button class="hover:text-brand-600" @click="doLogout">Logout</button>
        </template>
        <template v-else>
          <router-link to="/login" class="hover:text-brand-600">Login</router-link>
          <router-link to="/register" class="hover:text-brand-600">Register</router-link>
        </template>
      </div>

      <button class="md:hidden" @click="menuOpen = !menuOpen">☰</button>
    </div>

    <div v-if="menuOpen" class="md:hidden border-t border-slate-200 dark:border-slate-800 px-4 py-3 flex flex-col gap-2 text-sm">
      <router-link to="/" @click="menuOpen = false">Dashboard</router-link>
      <router-link to="/screener" @click="menuOpen = false">Screener</router-link>
      <router-link to="/shariah" @click="menuOpen = false">Shariah</router-link>
      <router-link v-if="auth.isAuthenticated" to="/watchlist" @click="menuOpen = false">Watchlist</router-link>
      <router-link v-if="auth.isAuthenticated" to="/alerts" @click="menuOpen = false">Alerts</router-link>
      <router-link v-if="auth.isAuthenticated" to="/portfolio" @click="menuOpen = false">Portfolio</router-link>
      <router-link v-if="auth.isAdmin" to="/admin/shariah-import" @click="menuOpen = false">Admin</router-link>
      <template v-if="auth.isAuthenticated">
        <button class="text-left" @click="doLogout(); menuOpen = false">Logout</button>
      </template>
      <template v-else>
        <router-link to="/login" @click="menuOpen = false">Login</router-link>
        <router-link to="/register" @click="menuOpen = false">Register</router-link>
      </template>
    </div>
  </header>
</template>
