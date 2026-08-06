<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { api } from '../lib/api'
import { useAuthStore } from '../stores/auth'
import ShariahBadge from '../components/ShariahBadge.vue'
import TechnicalChart from '../components/TechnicalChart.vue'

const props = defineProps<{ stockCode: string }>()
const auth = useAuthStore()

const tab = ref<'overview' | 'fundamentals' | 'technicals' | 'shariah'>('overview')
const company = ref<any>(null)
const shariahCurrent = ref<any>(null)
const fundamentals = ref<any[]>([])
const technicals = ref<{ candles: any[]; indicators: any } | null>(null)
const shariahHistory = ref<any[]>([])
const loading = ref(true)
const notFound = ref(false)

const watchlists = ref<any[]>([])
const selectedWatchlistId = ref<number | null>(null)
const newWatchlistName = ref('')
const addStatus = ref('')

async function loadCompany() {
  loading.value = true
  notFound.value = false
  try {
    const { data } = await api.get(`/companies/${props.stockCode}`)
    company.value = data
    const shariah = await api.get(`/shariah/status/${props.stockCode}`)
    shariahCurrent.value = shariah.data.current
    shariahHistory.value = shariah.data.history
  } catch {
    notFound.value = true
  } finally {
    loading.value = false
  }
}

async function loadFundamentals() {
  const { data } = await api.get(`/companies/${props.stockCode}/fundamentals`)
  fundamentals.value = data.periods
}

async function loadTechnicals() {
  const { data } = await api.get(`/companies/${props.stockCode}/technicals`)
  technicals.value = data
}

async function loadWatchlists() {
  if (!auth.isAuthenticated) return
  const { data } = await api.get('/watchlists')
  watchlists.value = data
  if (data.length) selectedWatchlistId.value = data[0].id
}

watch(tab, (t) => {
  if (t === 'fundamentals' && !fundamentals.value.length) loadFundamentals()
  if (t === 'technicals' && !technicals.value) loadTechnicals()
})

watch(() => props.stockCode, loadCompany)

onMounted(() => {
  loadCompany()
  loadWatchlists()
})

async function addToWatchlist() {
  addStatus.value = ''
  let watchlistId = selectedWatchlistId.value

  if (!watchlistId && newWatchlistName.value.trim()) {
    const { data } = await api.post('/watchlists', { name: newWatchlistName.value.trim() })
    watchlistId = data.id
    watchlists.value.push(data)
    newWatchlistName.value = ''
  }

  if (!watchlistId) {
    addStatus.value = 'Select or create a watchlist first.'
    return
  }

  try {
    await api.post(`/watchlists/${watchlistId}/items`, { security_id: company.value.security.id })
    addStatus.value = 'Added to watchlist.'
  } catch (e: any) {
    addStatus.value = e?.response?.data?.message ?? 'Could not add to watchlist.'
  }
}
</script>

<template>
  <div v-if="loading" class="text-slate-500">Loading…</div>
  <div v-else-if="notFound" class="text-noncompliant">No company found for stock code "{{ stockCode }}".</div>

  <div v-else-if="company">
    <div class="flex flex-wrap items-center gap-3 mb-1">
      <h1 class="text-xl font-semibold">{{ company.name }} ({{ company.stock_code }})</h1>
      <ShariahBadge :status="shariahCurrent?.status" />
    </div>
    <p class="text-sm text-slate-500 mb-4">
      {{ company.market?.sub_market }} Market · {{ company.sector?.name }}
    </p>

    <div class="border-b border-slate-200 dark:border-slate-800 flex gap-4 mb-4 text-sm">
      <button :class="tab === 'overview' ? 'border-b-2 border-brand-600 font-medium' : 'text-slate-500'" class="pb-2" @click="tab = 'overview'">Overview</button>
      <button :class="tab === 'fundamentals' ? 'border-b-2 border-brand-600 font-medium' : 'text-slate-500'" class="pb-2" @click="tab = 'fundamentals'">Fundamentals</button>
      <button :class="tab === 'technicals' ? 'border-b-2 border-brand-600 font-medium' : 'text-slate-500'" class="pb-2" @click="tab = 'technicals'">Technicals</button>
      <button :class="tab === 'shariah' ? 'border-b-2 border-brand-600 font-medium' : 'text-slate-500'" class="pb-2" @click="tab = 'shariah'">Shariah</button>
    </div>

    <div v-if="tab === 'overview'" class="space-y-4 text-sm">
      <p>{{ company.overview }}</p>
      <p><strong>Business segments:</strong> {{ company.business_segments }}</p>
      <p><strong>Listed:</strong> {{ company.listing_date?.slice(0, 10) }}</p>
      <div>
        <strong>Management:</strong>
        <ul class="list-disc list-inside">
          <li v-for="m in company.management" :key="m.name">{{ m.name }} — {{ m.title }}</li>
        </ul>
      </div>
      <div>
        <strong>Major Shareholders:</strong>
        <ul class="list-disc list-inside">
          <li v-for="s in company.major_shareholders" :key="s.name">{{ s.name }} — {{ s.holding_pct }}%</li>
        </ul>
      </div>

      <div v-if="auth.isAuthenticated" class="border border-slate-200 dark:border-slate-800 rounded p-3 max-w-sm">
        <h3 class="font-medium mb-2">Add to Watchlist</h3>
        <select v-model="selectedWatchlistId" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1 mb-2 text-sm">
          <option :value="null">— Create new —</option>
          <option v-for="w in watchlists" :key="w.id" :value="w.id">{{ w.name }}</option>
        </select>
        <input v-if="!selectedWatchlistId" v-model="newWatchlistName" placeholder="New watchlist name" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1 mb-2 text-sm" />
        <button class="bg-brand-600 text-white rounded px-3 py-1 text-sm" @click="addToWatchlist">Add</button>
        <p v-if="addStatus" class="text-xs mt-2 text-slate-500">{{ addStatus }}</p>
      </div>
      <router-link v-else to="/login" class="text-brand-600 text-sm">Log in to add this to a watchlist</router-link>
    </div>

    <div v-else-if="tab === 'fundamentals'" class="overflow-x-auto">
      <table class="w-full text-sm border-collapse">
        <thead>
          <tr class="text-left border-b border-slate-200 dark:border-slate-800">
            <th class="py-1 pr-4">Period</th>
            <th class="py-1 pr-4">Revenue</th>
            <th class="py-1 pr-4">Net Profit</th>
            <th class="py-1 pr-4">Net Margin</th>
            <th class="py-1 pr-4">EPS</th>
            <th class="py-1 pr-4">ROE</th>
            <th class="py-1 pr-4">PE</th>
            <th class="py-1 pr-4">PB</th>
            <th class="py-1 pr-4">Div/Share</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="f in fundamentals" :key="f.period_end" class="border-b border-slate-100 dark:border-slate-900">
            <td class="py-1 pr-4">{{ f.period_end }}</td>
            <td class="py-1 pr-4">{{ f.revenue?.toLocaleString() }}</td>
            <td class="py-1 pr-4">{{ f.net_profit?.toLocaleString() }}</td>
            <td class="py-1 pr-4">{{ f.net_margin }}%</td>
            <td class="py-1 pr-4">{{ f.eps }}</td>
            <td class="py-1 pr-4">{{ f.roe }}%</td>
            <td class="py-1 pr-4">{{ f.pe ?? '—' }}</td>
            <td class="py-1 pr-4">{{ f.pb ?? '—' }}</td>
            <td class="py-1 pr-4">{{ f.dividend_per_share }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else-if="tab === 'technicals'">
      <TechnicalChart v-if="technicals" :candles="technicals.candles" :indicators="technicals.indicators" />
      <p v-else class="text-slate-500">Loading chart…</p>
    </div>

    <div v-else-if="tab === 'shariah'" class="text-sm space-y-4">
      <div>
        <strong>Current status:</strong> <ShariahBadge :status="shariahCurrent?.status" />
        <span v-if="shariahCurrent" class="text-slate-500 ml-2">
          Source: SC Malaysia list, published {{ shariahCurrent.source_publication_date }}
        </span>
      </div>
      <div>
        <strong>History</strong>
        <ul class="mt-1 space-y-1">
          <li v-for="h in shariahHistory" :key="h.source_publication_date">
            {{ h.source_publication_date }} — {{ h.status === 'compliant' ? 'Compliant' : 'Non-Compliant' }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>
