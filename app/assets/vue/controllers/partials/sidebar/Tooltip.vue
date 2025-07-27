<script setup lang='ts'>

defineProps({
  text: {
    type: String,
    required: true
  },
  marginLeft: {
    type: String,
    default: 'ml-6'
  },
})

import { useSidebarStore } from '@/vue/stores/sidebar'

const sidebarStore = useSidebarStore();
</script>

<template>
  <div v-if="!sidebarStore.isExpanded" :class="marginLeft"
    class="rounded-md absolute top-[50%] -translate-y-[50%] whitespace-nowrap px-2 py-1 bg-indigo-100 text-indigo-800 text-sm invisible opacity-20 -translate-x-3 transition-all group-hover/navitem:visible group-hover/navitem:opacity-100 group-hover/navitem:translate-x-0 group-focus-within/navitem:visible group-focus-within/navitem:opacity-100 group-focus-within/navitem:translate-x-0">
    {{ text }}
  </div>
</template>

<style lang='scss' scoped></style>
