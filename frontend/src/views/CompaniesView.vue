<script setup lang="ts">
import { ref, watch } from 'vue'
import { api } from '../lib/api'

const companies = ref<any[]>([])
const q = ref('')
const page = ref(1)
const meta = ref({ current_page: 1, per_page: 20, total: 0 })

async function load() {
  const { data } = await api.get('/companies', { params: { q: q.value || undefined, page: page.value, per_page: 20 } })
  companies.value = data.data
  meta.value = data.meta
}

watch(q, () => { page.value = 1; load() })
watch(page, load)
load()

const lastPage = () => Math.max(1, Math.ceil(meta.value.total / meta.value.per_page))
</script>

<template>
  <div>
    <h1 class="text-xl font-semibold mb-1">Companies</h1>
    <p class="text-sm text-slate-500 mb-4">{{ meta.total }} Bursa Malaysia-listed companies.</p>

    <input v-model="q" placeholder="Search by name or stock code..." class="w-full max-w-sm border border-slate-300 dark:border-slate-700 bg-transparent rounded px-3 py-1.5 text-sm mb-4" />

    <table class="w-full text-sm border-collapse">
      <thead>
        <tr class="text-left border-b border-slate-200 dark:border-slate-800">
          <th class="py-1 pr-4">Stock</th>
          <th class="py-1 pr-4">Name</th>
          <th class="py-1 pr-4">Market</th>
          <th class="py-1 pr-4">Sector</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="c in companies" :key="c.stock_code" class="border-b border-slate-100 dark:border-slate-900">
          <td class="py-1 pr-4">
            <router-link :to="{ name: 'company-profile', params: { stockCode: c.stock_code } }" class="hover:underline">{{ c.stock_code }}</router-link>
          </td>
          <td class="py-1 pr-4">{{ c.name }}</td>
          <td class="py-1 pr-4">{{ c.market?.sub_market ?? '—' }}</td>
          <td class="py-1 pr-4">{{ c.sector?.name ?? '—' }}</td>
        </tr>
        <tr v-if="!companies.length"><td colspan="4" class="py-3 text-slate-500">No companies match.</td></tr>
      </tbody>
    </table>

    <div class="flex items-center gap-3 mt-4 text-sm">
      <button :disabled="page <= 1" class="disabled:opacity-40" @click="page--">Prev</button>
      <span>Page {{ meta.current_page }} of {{ lastPage() }}</span>
      <button :disabled="page >= lastPage()" class="disabled:opacity-40" @click="page++">Next</button>
    </div>
  </div>
</template>
