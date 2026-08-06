<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { api } from '../lib/api'

interface PortfolioSummary { id: number; name: string; market_value: number; unrealized_gain: number; unrealized_gain_pct: number | null }
interface Holding {
  stock_code: string; name: string; sector: string | null; quantity: number; avg_cost: number | null
  cost_basis: number; latest_price: number | null; market_value: number | null
  unrealized_gain: number | null; unrealized_gain_pct: number | null; realized_gain: number; allocation_pct: number | null
}
interface Totals { market_value: number; cost_basis: number; unrealized_gain: number; unrealized_gain_pct: number | null; realized_gain: number }
interface SectorAllocation { sector: string; market_value: number; pct: number }
interface TransactionRow { id: number; stock_code: string; name: string; type: string; quantity: number; price: number; transaction_date: string; notes: string | null }

const portfolios = ref<PortfolioSummary[]>([])
const activeId = ref<number | null>(null)
const detail = ref<{ holdings: Holding[]; sector_allocation: SectorAllocation[]; totals: Totals; transactions: TransactionRow[] } | null>(null)
const newName = ref('')

const searchQuery = ref('')
const searchResults = ref<any[]>([])
const txnForm = ref({ security_id: '', type: 'buy', quantity: '', price: '', transaction_date: new Date().toISOString().slice(0, 10), notes: '' })
const selectedSecurity = ref<{ stock_code: string; name: string } | null>(null)

async function loadPortfolios() {
  const { data } = await api.get('/portfolios')
  portfolios.value = data
  if (data.length && !activeId.value) activeId.value = data[0].id
  if (activeId.value) await loadDetail()
}

async function loadDetail() {
  if (!activeId.value) { detail.value = null; return }
  const { data } = await api.get(`/portfolios/${activeId.value}`)
  detail.value = data
}

async function selectPortfolio(id: number) {
  activeId.value = id
  await loadDetail()
}

async function createPortfolio() {
  if (!newName.value.trim()) return
  const { data } = await api.post('/portfolios', { name: newName.value.trim() })
  newName.value = ''
  await loadPortfolios()
  await selectPortfolio(data.id)
}

async function deletePortfolio() {
  if (!activeId.value) return
  if (!confirm('Delete this portfolio and all its transactions?')) return
  await api.delete(`/portfolios/${activeId.value}`)
  activeId.value = null
  await loadPortfolios()
}

async function search() {
  if (!searchQuery.value.trim()) { searchResults.value = []; return }
  const { data } = await api.get('/companies', { params: { q: searchQuery.value, per_page: 5 } })
  searchResults.value = data.data
}

function pickSecurity(company: any) {
  selectedSecurity.value = { stock_code: company.stock_code, name: company.name }
  txnForm.value.security_id = company.security.id
  searchQuery.value = ''
  searchResults.value = []
}

async function addTransaction() {
  if (!activeId.value || !txnForm.value.security_id) return
  await api.post(`/portfolios/${activeId.value}/transactions`, txnForm.value)
  txnForm.value = { security_id: '', type: 'buy', quantity: '', price: '', transaction_date: new Date().toISOString().slice(0, 10), notes: '' }
  selectedSecurity.value = null
  await loadDetail()
  await loadPortfolios()
}

async function removeTransaction(txn: TransactionRow) {
  if (!activeId.value) return
  await api.delete(`/portfolios/${activeId.value}/transactions/${txn.id}`)
  await loadDetail()
  await loadPortfolios()
}

function gainClass(v: number | null) {
  if (v === null) return 'text-slate-400'
  return v >= 0 ? 'text-compliant' : 'text-noncompliant'
}

onMounted(loadPortfolios)
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-xl font-semibold mb-1">Portfolio Management</h1>
      <p class="text-sm text-slate-500">
        Average-cost method for holdings and gain/loss — see FRS Module 9. Tax estimation
        and AI rebalancing remain future enhancements.
      </p>
    </div>

    <div class="flex flex-wrap items-center gap-2 text-sm">
      <button
        v-for="p in portfolios" :key="p.id"
        :class="p.id === activeId ? 'font-semibold underline' : 'text-slate-500'"
        @click="selectPortfolio(p.id)"
      >
        {{ p.name }}
      </button>
      <span v-if="portfolios.length" class="text-slate-300">|</span>
      <input v-model="newName" placeholder="New portfolio name" class="border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1 text-sm" @keyup.enter="createPortfolio" />
      <button class="text-brand-600" @click="createPortfolio">+ New</button>
    </div>

    <template v-if="detail">
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4 border border-slate-200 dark:border-slate-800 rounded p-3 text-sm">
        <div><div class="text-slate-500">Market Value</div><div class="font-semibold">RM {{ detail.totals.market_value.toLocaleString() }}</div></div>
        <div><div class="text-slate-500">Cost Basis</div><div class="font-semibold">RM {{ detail.totals.cost_basis.toLocaleString() }}</div></div>
        <div><div class="text-slate-500">Unrealized G/L</div><div class="font-semibold" :class="gainClass(detail.totals.unrealized_gain)">RM {{ detail.totals.unrealized_gain.toLocaleString() }} ({{ detail.totals.unrealized_gain_pct ?? '—' }}%)</div></div>
        <div><div class="text-slate-500">Realized G/L</div><div class="font-semibold" :class="gainClass(detail.totals.realized_gain)">RM {{ detail.totals.realized_gain.toLocaleString() }}</div></div>
        <div><button class="text-noncompliant text-xs" @click="deletePortfolio">Delete Portfolio</button></div>
      </div>

      <div v-if="detail.sector_allocation.length">
        <h2 class="font-medium mb-2 text-sm">Sector Allocation</h2>
        <div class="space-y-1">
          <div v-for="s in detail.sector_allocation" :key="s.sector" class="flex items-center gap-2 text-sm">
            <span class="w-32 shrink-0">{{ s.sector }}</span>
            <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded h-4 overflow-hidden">
              <div class="bg-brand-600 h-4" :style="{ width: s.pct + '%' }"></div>
            </div>
            <span class="w-16 text-right shrink-0">{{ s.pct }}%</span>
          </div>
        </div>
      </div>

      <div>
        <h2 class="font-medium mb-2 text-sm">Holdings</h2>
        <table class="w-full text-sm border-collapse">
          <thead>
            <tr class="text-left border-b border-slate-200 dark:border-slate-800">
              <th class="py-1 pr-4">Stock</th>
              <th class="py-1 pr-4">Qty</th>
              <th class="py-1 pr-4">Avg Cost</th>
              <th class="py-1 pr-4">Price</th>
              <th class="py-1 pr-4">Market Value</th>
              <th class="py-1 pr-4">Unrealized G/L</th>
              <th class="py-1 pr-4">Allocation</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="h in detail.holdings" :key="h.stock_code" class="border-b border-slate-100 dark:border-slate-900">
              <td class="py-1 pr-4">
                <router-link :to="{ name: 'company-profile', params: { stockCode: h.stock_code } }" class="hover:underline">{{ h.name }} ({{ h.stock_code }})</router-link>
              </td>
              <td class="py-1 pr-4">{{ h.quantity.toLocaleString() }}</td>
              <td class="py-1 pr-4">{{ h.avg_cost }}</td>
              <td class="py-1 pr-4">{{ h.latest_price ?? '—' }}</td>
              <td class="py-1 pr-4">{{ h.market_value?.toLocaleString() ?? '—' }}</td>
              <td class="py-1 pr-4" :class="gainClass(h.unrealized_gain)">{{ h.unrealized_gain?.toLocaleString() ?? '—' }} ({{ h.unrealized_gain_pct ?? '—' }}%)</td>
              <td class="py-1 pr-4">{{ h.allocation_pct ?? '—' }}%</td>
            </tr>
            <tr v-if="!detail.holdings.length"><td colspan="7" class="py-3 text-slate-500">No open holdings. Add a buy transaction below.</td></tr>
          </tbody>
        </table>
      </div>

      <div class="border border-slate-200 dark:border-slate-800 rounded p-4 max-w-lg">
        <h2 class="font-medium mb-3 text-sm">Add Transaction</h2>
        <div class="relative mb-2">
          <input v-model="searchQuery" placeholder="Search security..." class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1 text-sm" @input="search" />
          <ul v-if="searchResults.length" class="absolute z-10 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded mt-1 w-full text-sm shadow">
            <li v-for="c in searchResults" :key="c.stock_code" class="px-3 py-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer" @click="pickSecurity(c)">
              {{ c.name }} ({{ c.stock_code }})
            </li>
          </ul>
        </div>
        <p v-if="selectedSecurity" class="text-xs text-slate-500 mb-2">Selected: {{ selectedSecurity.name }} ({{ selectedSecurity.stock_code }})</p>

        <form class="grid grid-cols-2 gap-2 text-sm" @submit.prevent="addTransaction">
          <select v-model="txnForm.type" class="border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1">
            <option value="buy">Buy</option>
            <option value="sell">Sell</option>
          </select>
          <input v-model="txnForm.transaction_date" type="date" required class="border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
          <input v-model="txnForm.quantity" type="number" step="0.0001" min="0" placeholder="Quantity" required class="border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
          <input v-model="txnForm.price" type="number" step="0.0001" min="0" placeholder="Price per share (RM)" required class="border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
          <input v-model="txnForm.notes" placeholder="Notes (optional)" class="col-span-2 border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
          <button type="submit" :disabled="!txnForm.security_id" class="col-span-2 bg-brand-600 text-white rounded px-4 py-1.5 disabled:opacity-50">Add Transaction</button>
        </form>
      </div>

      <div>
        <h2 class="font-medium mb-2 text-sm">Transaction History</h2>
        <table class="w-full text-sm border-collapse">
          <thead>
            <tr class="text-left border-b border-slate-200 dark:border-slate-800">
              <th class="py-1 pr-4">Date</th>
              <th class="py-1 pr-4">Stock</th>
              <th class="py-1 pr-4">Type</th>
              <th class="py-1 pr-4">Qty</th>
              <th class="py-1 pr-4">Price</th>
              <th class="py-1 pr-4"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="t in detail.transactions" :key="t.id" class="border-b border-slate-100 dark:border-slate-900">
              <td class="py-1 pr-4">{{ t.transaction_date }}</td>
              <td class="py-1 pr-4">{{ t.name }} ({{ t.stock_code }})</td>
              <td class="py-1 pr-4 capitalize">{{ t.type }}</td>
              <td class="py-1 pr-4">{{ t.quantity }}</td>
              <td class="py-1 pr-4">{{ t.price }}</td>
              <td class="py-1 pr-4"><button class="text-noncompliant" @click="removeTransaction(t)">Remove</button></td>
            </tr>
            <tr v-if="!detail.transactions.length"><td colspan="6" class="py-3 text-slate-500">No transactions yet.</td></tr>
          </tbody>
        </table>
      </div>
    </template>
    <p v-else class="text-slate-500 text-sm">Create a portfolio to get started.</p>
  </div>
</template>
