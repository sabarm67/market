<script setup lang="ts">
defineProps<{ status: string | null | undefined }>()
</script>

<template>
  <span
    v-if="status === 'compliant'"
    class="inline-flex items-center gap-1 rounded-full bg-green-100 dark:bg-green-950 px-2 py-0.5 text-xs font-medium text-compliant"
  >
    ✅ Compliant
  </span>
  <span
    v-else-if="status === 'non_compliant'"
    class="inline-flex items-center gap-1 rounded-full bg-red-100 dark:bg-red-950 px-2 py-0.5 text-xs font-medium text-noncompliant"
  >
    ❌ Non-Compliant
  </span>
  <span v-else class="inline-flex items-center gap-1 rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-xs text-slate-500">
    — Unclassified
  </span>
</template>
