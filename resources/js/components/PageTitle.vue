<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  title: { type: String, required: true },
  appName: { type: String, default: '' },
})

const fullTitle = computed(() => {
  const t = (props.title ?? '').trim()
  const a = (props.appName ?? '').trim()
  if (!a) return t
  if (!t) return a
  return `${t} - ${a}`
})
</script>

<template>
  <Head :title="fullTitle" />
</template>
