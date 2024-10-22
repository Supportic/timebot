<script setup lang='ts'>
const props = defineProps({
  passwordInputId: {
    type: String,
    required: true
  },

})
import {
  trans,
  LOGIN_FORM_FIELD_REVEAL_PASSWORD_BUTTON_TITLE_REVEAL,
  LOGIN_FORM_FIELD_REVEAL_PASSWORD_BUTTON_TITLE_CONCEAL,
} from '@/translator';

const passwordInput = document.getElementById(props.passwordInputId) as HTMLInputElement;

let title = ref(trans(
  LOGIN_FORM_FIELD_REVEAL_PASSWORD_BUTTON_TITLE_REVEAL,
  {},
  'login'
));

let isPasswordRevealed = ref(passwordInput.type === 'text');

function toggleInputFieldType() {
  isPasswordRevealed.value = !isPasswordRevealed.value;
  passwordInput.type = isPasswordRevealed.value ? 'text' : 'password';
  title.value = isPasswordRevealed.value ? trans(
    LOGIN_FORM_FIELD_REVEAL_PASSWORD_BUTTON_TITLE_CONCEAL,
    {},
    'login'
  ) : trans(
    LOGIN_FORM_FIELD_REVEAL_PASSWORD_BUTTON_TITLE_REVEAL,
    {},
    'login'
  )
}

</script>
<template>
  <button @click="toggleInputFieldType" class="text-xl text-[#bbb] hover:text-slate-500 pointer-events-auto" type="button" :title="title">
    <icon-mdi-eye v-if="!isPasswordRevealed" />
    <icon-mdi-eye-off v-if="isPasswordRevealed"
      :class="`${isPasswordRevealed ? 'text-slate-500' : ''}`" />
    <span class="sr-only">{{ title }}</span>
  </button>
</template>
<style lang='scss' scoped></style>
