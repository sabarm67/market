<script setup lang="ts">
import { ref } from 'vue'
import { api } from '../lib/api'

const file = ref<File | null>(null)
const preview = ref<{ import_id: string; total_parsed: number; changes: any[] } | null>(null)
const error = ref('')
const commitMessage = ref('')

function onFileChange(e: Event) {
  const target = e.target as HTMLInputElement
  file.value = target.files?.[0] ?? null
  preview.value = null
  commitMessage.value = ''
}

async function uploadAndPreview() {
  error.value = ''
  preview.value = null
  if (!file.value) return

  const formData = new FormData()
  formData.append('file', file.value)

  try {
    const { data } = await api.post('/shariah/import', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    preview.value = data
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? 'Upload failed.'
  }
}

async function commit() {
  if (!preview.value) return
  try {
    const { data } = await api.post(`/shariah/import/${preview.value.import_id}/commit`)
    commitMessage.value = `Committed ${data.records_committed} records.`
    preview.value = null
    file.value = null
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? 'Commit failed.'
  }
}
</script>

<template>
  <div class="max-w-2xl">
    <h1 class="text-xl font-semibold mb-1">Admin: Shariah List Import</h1>
    <p class="text-sm text-slate-500 mb-4">
      Upload the SC Malaysia Shariah list as CSV with columns: <code>stock_code,status,source_publication_date</code>
      (status: <code>compliant</code> or <code>non_compliant</code>).
    </p>

    <input type="file" accept=".csv" @change="onFileChange" class="text-sm mb-3" />
    <div>
      <button class="bg-brand-600 text-white rounded px-4 py-1.5 text-sm" :disabled="!file" @click="uploadAndPreview">
        Upload &amp; Preview
      </button>
    </div>

    <p v-if="error" class="text-noncompliant text-sm mt-3">{{ error }}</p>
    <p v-if="commitMessage" class="text-compliant text-sm mt-3">{{ commitMessage }}</p>

    <div v-if="preview" class="mt-6">
      <p class="text-sm mb-2">{{ preview.total_parsed }} securities parsed, {{ preview.changes.length }} status changes detected.</p>
      <table class="w-full text-sm border-collapse mb-4">
        <thead>
          <tr class="text-left border-b border-slate-200 dark:border-slate-800">
            <th class="py-1 pr-4">Stock</th>
            <th class="py-1 pr-4">Name</th>
            <th class="py-1 pr-4">Old Status</th>
            <th class="py-1 pr-4">New Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in preview.changes" :key="c.stock_code" class="border-b border-slate-100 dark:border-slate-900">
            <td class="py-1 pr-4">{{ c.stock_code }}</td>
            <td class="py-1 pr-4">{{ c.name }}</td>
            <td class="py-1 pr-4">{{ c.old_status }}</td>
            <td class="py-1 pr-4">{{ c.new_status }}</td>
          </tr>
          <tr v-if="!preview.changes.length"><td colspan="4" class="py-2 text-slate-500">No status changes in this file.</td></tr>
        </tbody>
      </table>
      <div class="flex gap-2">
        <button class="bg-brand-600 text-white rounded px-4 py-1.5 text-sm" @click="commit">Confirm &amp; Commit</button>
        <button class="text-slate-500 text-sm" @click="preview = null">Cancel</button>
      </div>
    </div>
  </div>
</template>
