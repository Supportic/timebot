<script setup lang='ts'>

defineProps({
  text: {
    type: String,
    required: true
  },
})

import { useSidebarStore } from '@/vue/stores/sidebar'

const sidebarStore = useSidebarStore();
</script>

<template>
  <div v-if="!sidebarStore.isExpanded"
    class="rounded-md px-2 py-1 ml-6 bg-indigo-100 text-indigo-800 text-sm invisible opacity-20 -translate-x-3 transition-all group-hover:visible group-hover:opacity-100 group-hover:translate-x-0">
    {{ text }}
  </div>
</template>

<style lang='scss' scoped></style>
