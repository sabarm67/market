<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { api } from '../lib/api'

interface WatchlistItemOption { id: number; stock_code: string; name: string }
interface AlertRule {
  id: number
  watchlist_item_id: number
  stock_code: string
  name: string
  type: string
  direction: string | null
  threshold: number | null
  active: boolean
}
interface AlertTriggerRow {
  id: number
  stock_code: string
  type: string
  trigger_date: string
  message: string
  read_at: string | null
}

const TYPE_LABELS: Record<string, string> = {
  price_change_pct: 'Price Change %',
  volume_spike: 'Volume Spike',
  new_52w_high: 'New 52-Week High',
  new_52w_low: 'New 52-Week Low',
  shariah_status_change: 'Shariah Status Change',
}

const watchlistItems = ref<WatchlistItemOption[]>([])
const rules = ref<AlertRule[]>([])
const triggers = ref<AlertTriggerRow[]>([])

const form = ref({
  watchlist_item_id: '',
  type: 'price_change_pct',
  direction: 'either',
  threshold: '',
})

const needsThreshold = computed(() => ['price_change_pct', 'volume_spike'].includes(form.value.type))
const needsDirection = computed(() => form.value.type === 'price_change_pct')

async function loadWatchlistItems() {
  const { data } = await api.get('/watchlists')
  watchlistItems.value = data.flatMap((w: any) => w.items.map((i: any) => ({ id: i.id, stock_code: i.stock_code, name: i.name })))
}

async function loadRules() {
  const { data } = await api.get('/alerts/rules')
  rules.value = data
}

async function loadTriggers() {
  const { data } = await api.get('/alerts/triggers')
  triggers.value = data
}

async function createRule() {
  const payload: Record<string, any> = {
    watchlist_item_id: form.value.watchlist_item_id,
    type: form.value.type,
  }
  if (needsDirection.value) payload.direction = form.value.direction
  if (needsThreshold.value) payload.threshold = form.value.threshold

  await api.post('/alerts/rules', payload)
  form.value.threshold = ''
  await loadRules()
}

async function toggleRule(rule: AlertRule) {
  await api.patch(`/alerts/rules/${rule.id}`, { active: !rule.active })
  await loadRules()
}

async function deleteRule(rule: AlertRule) {
  await api.delete(`/alerts/rules/${rule.id}`)
  await loadRules()
}

async function markRead(trigger: AlertTriggerRow) {
  await api.post(`/alerts/triggers/${trigger.id}/read`)
  await loadTriggers()
}

onMounted(() => {
  loadWatchlistItems()
  loadRules()
  loadTriggers()
})
</script>

<template>
  <div class="space-y-8">
    <div>
      <h1 class="text-xl font-semibold mb-1">Watchlist Alerts</h1>
      <p class="text-sm text-slate-500">
        Evaluated once daily against end-of-day data. Triggered alerts arrive as a single
        daily email digest, not one email per alert.
      </p>
    </div>

    <div class="border border-slate-200 dark:border-slate-800 rounded p-4 max-w-lg">
      <h2 class="font-medium mb-3 text-sm">Add Alert Rule</h2>
      <form class="space-y-3 text-sm" @submit.prevent="createRule">
        <div>
          <label class="block text-xs text-slate-500 mb-1">Security</label>
          <select v-model="form.watchlist_item_id" required class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1">
            <option value="" disabled>Select from your watchlists</option>
            <option v-for="item in watchlistItems" :key="item.id" :value="item.id">{{ item.name }} ({{ item.stock_code }})</option>
          </select>
        </div>
        <div>
          <label class="block text-xs text-slate-500 mb-1">Alert Type</label>
          <select v-model="form.type" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1">
            <option v-for="(label, key) in TYPE_LABELS" :key="key" :value="key">{{ label }}</option>
          </select>
        </div>
        <div v-if="needsDirection">
          <label class="block text-xs text-slate-500 mb-1">Direction</label>
          <select v-model="form.direction" class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1">
            <option value="either">Either</option>
            <option value="up">Up only</option>
            <option value="down">Down only</option>
          </select>
        </div>
        <div v-if="needsThreshold">
          <label class="block text-xs text-slate-500 mb-1">
            {{ form.type === 'price_change_pct' ? 'Threshold (%)' : 'Threshold (× average volume)' }}
          </label>
          <input v-model="form.threshold" type="number" step="0.1" min="0" required class="w-full border border-slate-300 dark:border-slate-700 bg-transparent rounded px-2 py-1" />
        </div>
        <button type="submit" class="bg-brand-600 text-white rounded px-4 py-1.5 text-sm">Add Rule</button>
      </form>
    </div>

    <div>
      <h2 class="font-medium mb-2 text-sm">My Alert Rules</h2>
      <table class="w-full text-sm border-collapse">
        <thead>
          <tr class="text-left border-b border-slate-200 dark:border-slate-800">
            <th class="py-1 pr-4">Security</th>
            <th class="py-1 pr-4">Type</th>
            <th class="py-1 pr-4">Condition</th>
            <th class="py-1 pr-4">Active</th>
            <th class="py-1 pr-4"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in rules" :key="r.id" class="border-b border-slate-100 dark:border-slate-900">
            <td class="py-1 pr-4">{{ r.name }} ({{ r.stock_code }})</td>
            <td class="py-1 pr-4">{{ TYPE_LABELS[r.type] }}</td>
            <td class="py-1 pr-4">
              <span v-if="r.type === 'price_change_pct'">{{ r.direction }} ≥ {{ r.threshold }}%</span>
              <span v-else-if="r.type === 'volume_spike'">≥ {{ r.threshold }}x avg</span>
              <span v-else>—</span>
            </td>
            <td class="py-1 pr-4">
              <button class="text-xs px-2 py-0.5 rounded" :class="r.active ? 'bg-green-100 text-compliant' : 'bg-slate-100 text-slate-500'" @click="toggleRule(r)">
                {{ r.active ? 'Active' : 'Paused' }}
              </button>
            </td>
            <td class="py-1 pr-4"><button class="text-noncompliant" @click="deleteRule(r)">Remove</button></td>
          </tr>
          <tr v-if="!rules.length"><td colspan="5" class="py-3 text-slate-500">No alert rules yet — add one above.</td></tr>
        </tbody>
      </table>
    </div>

    <div>
      <h2 class="font-medium mb-2 text-sm">Recent Alerts</h2>
      <ul class="space-y-1 text-sm">
        <li v-for="t in triggers" :key="t.id" class="flex items-center justify-between border-b border-slate-100 dark:border-slate-900 py-1.5" :class="{ 'text-slate-400': t.read_at }">
          <span>
            <span class="text-xs text-slate-500 mr-2">{{ t.trigger_date }}</span>{{ t.message }}
          </span>
          <button v-if="!t.read_at" class="text-xs text-brand-600 shrink-0 ml-2" @click="markRead(t)">Mark read</button>
        </li>
        <li v-if="!triggers.length" class="py-3 text-slate-500">No alerts triggered yet.</li>
      </ul>
    </div>
  </div>
</template>
