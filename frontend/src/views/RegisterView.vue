<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const error = ref('')
const auth = useAuthStore()
const router = useRouter()

async function submit() {
  error.value = ''
  try {
    await auth.register(name.value, email.value, password.value, passwordConfirmation.value)
    router.push({ name: 'dashboard' })
  } catch (e: any) {
    const errors = e?.response?.data?.errors
    error.value = errors ? Object.values(errors).flat().join(' ') : (e?.response?.data?.message ?? 'Registration failed.')
  }
}
</script>

<template>
  <div class="max-w-sm mx-auto">
    <h1 class="text-xl font-semibold mb-4">Register</h1>
    <form class="space-y-3" @submit.prevent="submit">
      <div>
        <label class="block text-sm mb-1">Name</label>
        <input v-model="name" required class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-3 py-1.5 text-sm" />
      </div>
      <div>
        <label class="block text-sm mb-1">Email</label>
        <input v-model="email" type="email" required class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-3 py-1.5 text-sm" />
      </div>
      <div>
        <label class="block text-sm mb-1">Password</label>
        <input v-model="password" type="password" required minlength="8" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-3 py-1.5 text-sm" />
      </div>
      <div>
        <label class="block text-sm mb-1">Confirm Password</label>
        <input v-model="passwordConfirmation" type="password" required class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-3 py-1.5 text-sm" />
      </div>
      <p v-if="error" class="text-noncompliant text-sm">{{ error }}</p>
      <button type="submit" class="bg-brand-600 text-white rounded px-4 py-1.5 text-sm">Register</button>
    </form>
    <p class="text-sm text-slate-500 mt-3">
      Already have an account? <router-link to="/login" class="text-brand-600">Log in</router-link>
    </p>
  </div>
</template>
