<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { api } from '../lib/api'

interface MoverItem {
  stock_code: string
  name: string
  sector: string | null
  change_pct: number | null
  volume: number
}

interface DashboardData {
  as_of: string | null
  top_gainers: MoverItem[]
  top_losers: MoverItem[]
  top_volume: MoverItem[]
  sector_performance: { sector: string; change_pct: number }[]
  breadth: { advancers: number; decliners: number; new_highs_52w: number; new_lows_52w: number }
}

const data = ref<DashboardData | null>(null)
const loading = ref(true)
const error = ref('')

async function load() {
  loading.value = true
  error.value = ''
  try {
    const { data: res } = await api.get<DashboardData>('/dashboard')
    data.value = res
  } catch (e) {
    error.value = 'Failed to load dashboard data.'
  } finally {
    loading.value = false
  }
}

onMounted(load)

function pctClass(pct: number | null) {
  if (pct === null) return 'text-slate-400'
  return pct >= 0 ? 'text-compliant' : 'text-noncompliant'
}
</script>

<template>
  <div>
    <div class="flex items-baseline justify-between mb-4">
      <h1 class="text-xl font-semibold">Market Dashboard</h1>
      <span v-if="data?.as_of" class="text-xs text-slate-500">as of: {{ data.as_of }}</span>
    </div>

    <p v-if="loading" class="text-slate-500">Loading…</p>
    <p v-else-if="error" class="text-noncompliant">{{ error }}</p>

    <template v-else-if="data">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="border border-slate-200 dark:border-slate-800 rounded p-3">
          <h2 class="font-medium mb-2 text-sm">Top Gainers</h2>
          <ul class="text-sm space-y-1">
            <li v-for="m in data.top_gainers" :key="m.stock_code" class="flex justify-between">
              <router-link :to="{ name: 'company-profile', params: { stockCode: m.stock_code } }" class="hover:underline">
                {{ m.name }} ({{ m.stock_code }})
              </router-link>
              <span :class="pctClass(m.change_pct)">{{ m.change_pct }}%</span>
            </li>
          </ul>
        </div>
        <div class="border border-slate-200 dark:border-slate-800 rounded p-3">
          <h2 class="font-medium mb-2 text-sm">Top Losers</h2>
          <ul class="text-sm space-y-1">
            <li v-for="m in data.top_losers" :key="m.stock_code" class="flex justify-between">
              <router-link :to="{ name: 'company-profile', params: { stockCode: m.stock_code } }" class="hover:underline">
                {{ m.name }} ({{ m.stock_code }})
              </router-link>
              <span :class="pctClass(m.change_pct)">{{ m.change_pct }}%</span>
            </li>
          </ul>
        </div>
        <div class="border border-slate-200 dark:border-slate-800 rounded p-3">
          <h2 class="font-medium mb-2 text-sm">Top Volume</h2>
          <ul class="text-sm space-y-1">
            <li v-for="m in data.top_volume" :key="m.stock_code" class="flex justify-between">
              <router-link :to="{ name: 'company-profile', params: { stockCode: m.stock_code } }" class="hover:underline">
                {{ m.name }} ({{ m.stock_code }})
              </router-link>
              <span>{{ m.volume.toLocaleString() }}</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="border border-slate-200 dark:border-slate-800 rounded p-3 mb-6">
        <h2 class="font-medium mb-2 text-sm">Sector Performance</h2>
        <ul class="text-sm space-y-1">
          <li v-for="s in data.sector_performance" :key="s.sector" class="flex justify-between">
            <span>{{ s.sector }}</span>
            <span :class="pctClass(s.change_pct)">{{ s.change_pct }}%</span>
          </li>
        </ul>
      </div>

      <div class="border border-slate-200 dark:border-slate-800 rounded p-3 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div><div class="text-slate-500">Advancers</div><div class="font-semibold">{{ data.breadth.advancers }}</div></div>
        <div><div class="text-slate-500">Decliners</div><div class="font-semibold">{{ data.breadth.decliners }}</div></div>
        <div><div class="text-slate-500">52W Highs</div><div class="font-semibold">{{ data.breadth.new_highs_52w }}</div></div>
        <div><div class="text-slate-500">52W Lows</div><div class="font-semibold">{{ data.breadth.new_lows_52w }}</div></div>
      </div>
    </template>
  </div>
</template>
