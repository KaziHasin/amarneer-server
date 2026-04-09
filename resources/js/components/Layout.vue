<script setup>
import AppHeader from '@/components/AppHeader.vue'
import AppSidebar from '@/components/AppSidebar.vue'
import PageTitle from '@/components/PageTitle.vue'

const props = defineProps({
  title: { type: String, default: '' },
  appName: { type: String, default: 'AdminPanel' },
  withChrome: { type: Boolean, default: true },
})
</script>

<template>
  <PageTitle :title="title" :appName="appName" />

  <div v-if="withChrome" class="app-shell">
    <AppHeader />
    <AppSidebar />
    <main class="main-content">
      <slot />
    </main>
  </div>

  <slot v-else />
</template>

<style scoped>
.app-shell {
  min-height: 100vh;
}
</style>
