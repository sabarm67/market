<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const email = ref('')
const password = ref('')
const error = ref('')
const auth = useAuthStore()
const router = useRouter()
const route = useRoute()

async function submit() {
  error.value = ''
  try {
    await auth.login(email.value, password.value)
    router.push((route.query.redirect as string) || { name: 'dashboard' })
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? 'Login failed.'
  }
}
</script>

<template>
  <div class="max-w-sm mx-auto">
    <h1 class="text-xl font-semibold mb-4">Log In</h1>
    <form class="space-y-3" @submit.prevent="submit">
      <div>
        <label class="block text-sm mb-1">Email</label>
        <input v-model="email" type="email" required class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-3 py-1.5 text-sm" />
      </div>
      <div>
        <label class="block text-sm mb-1">Password</label>
        <input v-model="password" type="password" required class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-3 py-1.5 text-sm" />
      </div>
      <p v-if="error" class="text-noncompliant text-sm">{{ error }}</p>
      <button type="submit" class="bg-brand-600 text-white rounded px-4 py-1.5 text-sm">Log In</button>
    </form>
    <p class="text-sm text-slate-500 mt-3">
      No account? <router-link to="/register" class="text-brand-600">Register</router-link>
    </p>
    <p class="text-xs text-slate-400 mt-6">Demo accounts: investor@example.com / password (registered), admin@example.com / password (admin)</p>
  </div>
</template>
