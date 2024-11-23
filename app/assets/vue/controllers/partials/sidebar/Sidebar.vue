<script setup lang='ts'>
import ToggleSidebarButton from '@components/ToggleSidebarButton.vue';

const props = defineProps({
  isExpanded: {
    type: Boolean,
    required: true
  },
})
</script>

<template>
  <ToggleSidebarButton :isExpanded="props.isExpanded"></ToggleSidebarButton>
</template>

<style lang='scss' scoped></style>
