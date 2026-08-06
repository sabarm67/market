<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { api } from '../lib/api'
import ShariahBadge from '../components/ShariahBadge.vue'

interface Sector { id: number; name: string; industry: string | null }
interface ScreenerRow {
  stock_code: string
  name: string
  sector: string | null
  sub_market: string | null
  shariah_status: string | null
  price: number
  volume: number
  market_cap: number | null
  pe: number | null
  pb: number | null
  roe: number
  debt_equity: number
  dividend_yield: number | null
  revenue_growth_pct: number | null
  rsi: number | null
}

const sectors = ref<Sector[]>([])
const results = ref<ScreenerRow[]>([])
const total = ref(0)
const loading = ref(false)

const filters = ref({
  sector_id: '',
  shariah_status: '',
  market_cap_min: '',
  pe_max: '',
  pb_max: '',
  roe_min: '',
  dividend_yield_min: '',
  debt_equity_max: '',
  revenue_growth_min: '',
  volume_min: '',
  rsi_min: '',
  rsi_max: '',
})

const sortBy = ref('stock_code')
const sortDir = ref<'asc' | 'desc'>('asc')

async function loadSectors() {
  const { data } = await api.get<Sector[]>('/sectors')
  sectors.value = data
}

async function runScreen() {
  loading.value = true
  const params: Record<string, string> = { sort_by: sortBy.value, sort_dir: sortDir.value }
  for (const [key, value] of Object.entries(filters.value)) {
    if (value !== '') params[key] = String(value)
  }
  try {
    const { data } = await api.get('/screener', { params })
    results.value = data.results
    total.value = data.total
  } finally {
    loading.value = false
  }
}

function sortByColumn(col: string) {
  if (sortBy.value === col) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = col
    sortDir.value = 'desc'
  }
  runScreen()
}

function resetFilters() {
  for (const key of Object.keys(filters.value) as (keyof typeof filters.value)[]) {
    filters.value[key] = ''
  }
  runScreen()
}

onMounted(() => {
  loadSectors()
  runScreen()
})
</script>

<template>
  <div>
    <h1 class="text-xl font-semibold mb-1">Stock Screener</h1>
    <p class="text-sm text-slate-500 mb-4">
      Multi-criteria filtering over Bursa Malaysia-listed securities. A custom formula
      builder is a future enhancement — this covers the standard criteria below.
    </p>

    <form class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4 text-sm" @submit.prevent="runScreen">
      <div>
        <label class="block text-xs text-slate-500 mb-1">Sector</label>
        <select v-model="filters.sector_id" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1">
          <option value="">Any</option>
          <option v-for="s in sectors" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
      </div>
      <div>
        <label class="block text-xs text-slate-500 mb-1">Shariah Status</label>
        <select v-model="filters.shariah_status" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1">
          <option value="">Any</option>
          <option value="compliant">Compliant</option>
          <option value="non_compliant">Non-Compliant</option>
        </select>
      </div>
      <div>
        <label class="block text-xs text-slate-500 mb-1">Market Cap ≥ (RM)</label>
        <input v-model="filters.market_cap_min" type="number" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
      </div>
      <div>
        <label class="block text-xs text-slate-500 mb-1">Volume ≥</label>
        <input v-model="filters.volume_min" type="number" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
      </div>
      <div>
        <label class="block text-xs text-slate-500 mb-1">PE ≤</label>
        <input v-model="filters.pe_max" type="number" step="0.1" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
      </div>
      <div>
        <label class="block text-xs text-slate-500 mb-1">PB ≤</label>
        <input v-model="filters.pb_max" type="number" step="0.1" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
      </div>
      <div>
        <label class="block text-xs text-slate-500 mb-1">ROE ≥ (%)</label>
        <input v-model="filters.roe_min" type="number" step="0.1" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
      </div>
      <div>
        <label class="block text-xs text-slate-500 mb-1">Dividend Yield ≥ (%)</label>
        <input v-model="filters.dividend_yield_min" type="number" step="0.1" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
      </div>
      <div>
        <label class="block text-xs text-slate-500 mb-1">Debt/Equity ≤</label>
        <input v-model="filters.debt_equity_max" type="number" step="0.1" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
      </div>
      <div>
        <label class="block text-xs text-slate-500 mb-1">Revenue Growth ≥ (%)</label>
        <input v-model="filters.revenue_growth_min" type="number" step="0.1" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
      </div>
      <div>
        <label class="block text-xs text-slate-500 mb-1">RSI(14) min</label>
        <input v-model="filters.rsi_min" type="number" step="1" min="0" max="100" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
      </div>
      <div>
        <label class="block text-xs text-slate-500 mb-1">RSI(14) max</label>
        <input v-model="filters.rsi_max" type="number" step="1" min="0" max="100" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
      </div>

      <div class="col-span-2 md:col-span-4 flex gap-2 mt-1">
        <button type="submit" class="bg-brand-600 text-white rounded px-4 py-1.5 text-sm">Apply Filters</button>
        <button type="button" class="text-slate-500 text-sm" @click="resetFilters">Reset</button>
      </div>
    </form>

    <p class="text-xs text-slate-500 mb-2">{{ total }} matching securities</p>

    <div class="overflow-x-auto">
      <table class="w-full text-sm border-collapse">
        <thead>
          <tr class="text-left border-b border-slate-200 dark:border-slate-800">
            <th class="py-1 pr-4">Stock</th>
            <th class="py-1 pr-4">Sector</th>
            <th class="py-1 pr-4">Shariah</th>
            <th class="py-1 pr-4 cursor-pointer select-none" @click="sortByColumn('market_cap')">Mkt Cap ⇅</th>
            <th class="py-1 pr-4 cursor-pointer select-none" @click="sortByColumn('pe')">PE ⇅</th>
            <th class="py-1 pr-4 cursor-pointer select-none" @click="sortByColumn('pb')">PB ⇅</th>
            <th class="py-1 pr-4 cursor-pointer select-none" @click="sortByColumn('roe')">ROE ⇅</th>
            <th class="py-1 pr-4 cursor-pointer select-none" @click="sortByColumn('dividend_yield')">Div Yield ⇅</th>
            <th class="py-1 pr-4 cursor-pointer select-none" @click="sortByColumn('revenue_growth_pct')">Rev Growth ⇅</th>
            <th class="py-1 pr-4 cursor-pointer select-none" @click="sortByColumn('rsi')">RSI ⇅</th>
            <th class="py-1 pr-4 cursor-pointer select-none" @click="sortByColumn('volume')">Volume ⇅</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in results" :key="r.stock_code" class="border-b border-slate-100 dark:border-slate-900">
            <td class="py-1 pr-4">
              <router-link :to="{ name: 'company-profile', params: { stockCode: r.stock_code } }" class="hover:underline">
                {{ r.name }} ({{ r.stock_code }})
              </router-link>
            </td>
            <td class="py-1 pr-4">{{ r.sector }}</td>
            <td class="py-1 pr-4"><ShariahBadge :status="r.shariah_status" /></td>
            <td class="py-1 pr-4">{{ r.market_cap ? (r.market_cap / 1_000_000).toFixed(1) + 'M' : '—' }}</td>
            <td class="py-1 pr-4">{{ r.pe ?? '—' }}</td>
            <td class="py-1 pr-4">{{ r.pb ?? '—' }}</td>
            <td class="py-1 pr-4">{{ r.roe }}%</td>
            <td class="py-1 pr-4">{{ r.dividend_yield ?? '—' }}%</td>
            <td class="py-1 pr-4">{{ r.revenue_growth_pct ?? '—' }}%</td>
            <td class="py-1 pr-4">{{ r.rsi ?? '—' }}</td>
            <td class="py-1 pr-4">{{ r.volume.toLocaleString() }}</td>
          </tr>
          <tr v-if="!loading && !results.length">
            <td colspan="11" class="py-3 text-slate-500">No securities match these filters.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
