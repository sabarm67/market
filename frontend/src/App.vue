<script setup lang="ts">
import NavBar from './components/NavBar.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <NavBar />
    <main class="flex-1 mx-auto w-full max-w-6xl px-4 py-6">
      <router-view />
    </main>
  </div>
</template>
