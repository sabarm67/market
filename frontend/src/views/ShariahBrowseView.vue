<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { api } from '../lib/api'
import ShariahBadge from '../components/ShariahBadge.vue'

const status = ref<'' | 'compliant' | 'non_compliant'>('')
const securities = ref<any[]>([])
const loading = ref(true)

async function load() {
  loading.value = true
  const { data } = await api.get('/shariah/securities', { params: status.value ? { status: status.value } : {} })
  securities.value = data
  loading.value = false
}

onMounted(load)
watch(status, load)
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold">Shariah-Compliant Securities</h1>
      <span class="text-xs text-slate-500">Source: SC Malaysia list</span>
    </div>

    <select v-model="status" class="border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1 text-sm mb-4">
      <option value="">All statuses</option>
      <option value="compliant">Compliant</option>
      <option value="non_compliant">Non-Compliant</option>
    </select>

    <p v-if="loading" class="text-slate-500">Loading…</p>
    <table v-else class="w-full text-sm border-collapse">
      <thead>
        <tr class="text-left border-b border-slate-200 dark:border-slate-800">
          <th class="py-1 pr-4">Stock</th>
          <th class="py-1 pr-4">Name</th>
          <th class="py-1 pr-4">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="s in securities" :key="s.stock_code" class="border-b border-slate-100 dark:border-slate-900">
          <td class="py-1 pr-4">
            <router-link :to="{ name: 'company-profile', params: { stockCode: s.stock_code } }" class="hover:underline">
              {{ s.stock_code }}
            </router-link>
          </td>
          <td class="py-1 pr-4">{{ s.name }}</td>
          <td class="py-1 pr-4"><ShariahBadge :status="s.status" /></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
