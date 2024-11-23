<script setup lang='ts'>

const props = defineProps({
  isExpanded: {
    type: Boolean,
    required: true
  },
})

import { useSidebarStore } from '@/vue/stores/sidebar'

const sidebarStore = useSidebarStore();

// let isExpanded = ref(props.isExpanded);
sidebarStore.isExpanded = props.isExpanded;


const appName = document.getElementById('app-name');
const appProfile = document.getElementById('app-profile');
const navLinks = document.querySelectorAll('.nav-link span');

const expand = () => {
  appName?.classList.replace('w-32', 'w-0');

  appProfile?.classList.add('w-0');
  appProfile?.classList.remove('w-52', 'ml-3');

  navLinks.forEach(navLink => {
    navLink?.classList.add('w-0');
    navLink?.classList.remove('w-52', 'ml-3');
  })
}

const contract = () => {
  appName?.classList.replace('w-0', 'w-32');

  appProfile?.classList.add('w-52', 'ml-3');
  appProfile?.classList.remove('w-0');

  navLinks.forEach(navLink => {
    navLink?.classList.add('w-52', 'ml-3');
    navLink?.classList.remove('w-0');
  })
}

const toggleSidebar = async () => {
  sidebarStore.isExpanded = !sidebarStore.isExpanded;
  sidebarStore.isExpanded ? contract() : expand();

  // save sidebar expanded sate in server session
  await fetch('/session/set', {
    method: 'POST',
    body: JSON.stringify({ 'sidebar_expanded': sidebarStore.isExpanded })
  }).catch(error => {
    console.error(error);
  })
}

</script>
<template>
  <button @click="toggleSidebar" id="sidebar-toggle" class="p-1.5 rounded-lg bg-gray-50 hover:bg-gray-100">
    <icon-lucide-chevron-first style="font-size: 1.4rem" v-if="sidebarStore.isExpanded" />
    <icon-lucide-chevron-last style="font-size: 1.4rem" v-if="!sidebarStore.isExpanded" />
  </button>
</template>
<style lang='scss' scoped></style>
