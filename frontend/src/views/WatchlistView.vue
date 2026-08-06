<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { api } from '../lib/api'
import ShariahBadge from '../components/ShariahBadge.vue'

const watchlists = ref<any[]>([])
const activeId = ref<number | null>(null)
const newName = ref('')
const searchQuery = ref('')
const searchResults = ref<any[]>([])

async function load() {
  const { data } = await api.get('/watchlists')
  watchlists.value = data
  if (data.length && !activeId.value) activeId.value = data[0].id
}

const active = () => watchlists.value.find((w) => w.id === activeId.value)

async function createWatchlist() {
  if (!newName.value.trim()) return
  const { data } = await api.post('/watchlists', { name: newName.value.trim() })
  watchlists.value.push(data)
  activeId.value = data.id
  newName.value = ''
}

async function deleteWatchlist() {
  if (!activeId.value) return
  if (!confirm('Delete this watchlist?')) return
  await api.delete(`/watchlists/${activeId.value}`)
  watchlists.value = watchlists.value.filter((w) => w.id !== activeId.value)
  activeId.value = watchlists.value[0]?.id ?? null
}

async function removeItem(itemId: number) {
  if (!activeId.value) return
  await api.delete(`/watchlists/${activeId.value}/items/${itemId}`)
  await load()
}

async function search() {
  if (!searchQuery.value.trim()) { searchResults.value = []; return }
  const { data } = await api.get('/companies', { params: { q: searchQuery.value, per_page: 5 } })
  searchResults.value = data.data
}

async function addSecurity(company: any) {
  if (!activeId.value) return
  await api.post(`/watchlists/${activeId.value}/items`, { security_id: company.security.id })
  searchQuery.value = ''
  searchResults.value = []
  await load()
}

onMounted(load)
</script>

<template>
  <div>
    <h1 class="text-xl font-semibold mb-4">My Watchlists</h1>

    <div class="flex flex-wrap items-center gap-2 mb-4 text-sm">
      <button
        v-for="w in watchlists" :key="w.id"
        :class="w.id === activeId ? 'font-semibold underline' : 'text-slate-500'"
        @click="activeId = w.id"
      >
        {{ w.name }}
      </button>
      <span class="text-slate-300">|</span>
      <input v-model="newName" placeholder="New watchlist name" class="border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1 text-sm" @keyup.enter="createWatchlist" />
      <button class="text-brand-600" @click="createWatchlist">+ New</button>
    </div>

    <div v-if="active()">
      <div class="relative max-w-xs mb-4">
        <input v-model="searchQuery" placeholder="Add security to this watchlist..." class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-3 py-1.5 text-sm" @input="search" />
        <ul v-if="searchResults.length" class="absolute z-10 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded mt-1 w-full text-sm shadow">
          <li v-for="c in searchResults" :key="c.stock_code" class="px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer" @click="addSecurity(c)">
            {{ c.name }} ({{ c.stock_code }})
          </li>
        </ul>
      </div>

      <table class="w-full text-sm border-collapse">
        <thead>
          <tr class="text-left border-b border-slate-200 dark:border-slate-800">
            <th class="py-1 pr-4">Stock</th>
            <th class="py-1 pr-4">Price</th>
            <th class="py-1 pr-4">Shariah</th>
            <th class="py-1 pr-4">Note</th>
            <th class="py-1 pr-4"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in active().items" :key="item.id" class="border-b border-slate-100 dark:border-slate-900">
            <td class="py-1 pr-4">
              <router-link :to="{ name: 'company-profile', params: { stockCode: item.stock_code } }" class="hover:underline">
                {{ item.name }} ({{ item.stock_code }})
              </router-link>
            </td>
            <td class="py-1 pr-4">{{ item.price ?? '—' }}</td>
            <td class="py-1 pr-4"><ShariahBadge :status="item.shariah_status" /></td>
            <td class="py-1 pr-4 text-slate-500">{{ item.note || '—' }}</td>
            <td class="py-1 pr-4"><button class="text-noncompliant" @click="removeItem(item.id)">Remove</button></td>
          </tr>
          <tr v-if="!active().items.length">
            <td colspan="5" class="py-3 text-slate-500">No securities yet — search above to add one.</td>
          </tr>
        </tbody>
      </table>

      <button class="text-noncompliant text-sm mt-4" @click="deleteWatchlist">Delete this watchlist</button>
    </div>
    <p v-else class="text-slate-500">Create a watchlist to get started.</p>
  </div>
</template>
