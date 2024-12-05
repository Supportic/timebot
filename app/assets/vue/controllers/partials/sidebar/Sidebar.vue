<script setup lang='ts'>
import ToggleSidebarButton from '@components/ToggleSidebarButton.vue';

defineProps({
  isExpanded: {
    type: Boolean,
    required: true
  },
})
</script>

<template>
  <ToggleSidebarButton :isExpanded></ToggleSidebarButton>
</template>

<style lang='scss' scoped></style>
