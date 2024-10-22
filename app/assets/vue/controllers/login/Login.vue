<script setup lang='ts'>
import TogglePasswordButton from '@components/TogglePasswordButton.vue';

const props = defineProps({
  passwordInputId: {
    type: String,
    required: true
  }
})
</script>

<template>
  <TogglePasswordButton :password-input-id="props.passwordInputId"></TogglePasswordButton>
</template>

<style lang='scss' scoped></style>
