import { defineStore, acceptHMRUpdate } from 'pinia';

interface State {
  isExpanded: boolean;
}

export const useSidebarStore = defineStore({
  id: 'sidebar',
  state: (): State => ({
    isExpanded: true,
  }),

  actions: {},
});

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useSidebarStore, import.meta.hot));
}
