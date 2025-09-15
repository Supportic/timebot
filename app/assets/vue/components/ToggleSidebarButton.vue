<script setup lang='ts'>

const props = defineProps({
  isExpanded: {
    type: Boolean,
    required: true
  },
})

import { useSidebarStore } from '@/vue/stores/sidebar'

const sidebarStore = useSidebarStore();

sidebarStore.isExpanded = props.isExpanded;

const appName = document.getElementById('sidebar-app-name');
const appProfile = document.getElementById('sidebar-user-profile');
const navLinks = document.querySelectorAll('.nav-link span');

const BREAKPOINT_MD = 768;

// minimize sidebar when user is resizing window to mobile viewport
const resizeObserver = new ResizeObserver((entries) => {

  for (const entry of entries) {
    if (
      entry.contentRect
      && sidebarStore.isExpanded
      && entry.contentRect.width < BREAKPOINT_MD
    ) {
      toggleSidebar();
    }
  }
});

resizeObserver.observe(document.body);

const expand = () => {
  appName?.classList.replace('w-32', 'w-0');
  appName?.nextElementSibling?.classList.add('mr-auto');

  appProfile?.classList.add('w-0');
  appProfile?.classList.remove('w-52', 'ml-3');

  navLinks.forEach(navLink => {
    navLink?.classList.add('w-0');
    navLink?.classList.remove('w-52', 'ml-3');
  })
}

const minimize = () => {
  appName?.classList.replace('w-0', 'w-32');
  appName?.nextElementSibling?.classList.remove('mr-auto');

  appProfile?.classList.add('w-52', 'ml-3');
  appProfile?.classList.remove('w-0');

  navLinks.forEach(navLink => {
    navLink?.classList.add('w-52', 'ml-3');
    navLink?.classList.remove('w-0');
  })
}

const toggleSidebar = () => {
  sidebarStore.isExpanded = !sidebarStore.isExpanded;
  sidebarStore.isExpanded ? minimize() : expand();

  sidebarStore.updateSession();
}

</script>
<template>
  <button @click="toggleSidebar" id="sidebar-toggle" class="p-1.5 rounded-lg bg-gray-50 hover:bg-gray-100">
    <icon-lucide-chevron-first style="font-size: 1.4rem" v-if="sidebarStore.isExpanded" />
    <icon-lucide-chevron-last style="font-size: 1.4rem" v-if="!sidebarStore.isExpanded" />
  </button>
</template>
<style lang='scss' scoped></style>
