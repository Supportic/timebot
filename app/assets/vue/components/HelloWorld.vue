<script setup lang="ts">
defineProps({
  msg: {
    type: String,
    required: true
  }
})
</script>

<template>
  <h1>{{ msg }}</h1>
</template>

<style lang="scss" scoped></style>