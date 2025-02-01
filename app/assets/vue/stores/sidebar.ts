import { defineStore, acceptHMRUpdate } from 'pinia';

interface State {
  isExpanded: boolean;
}

export const useSidebarStore = defineStore('sidebar', {
  state: (): State => ({
    isExpanded: true,
  }),

  actions: {
    // save sidebar expanded sate in server session
    async updateSession() {
      await fetch('/session/set', {
        method: 'POST',
        body: JSON.stringify({ sidebar_expanded: this.isExpanded }),
      }).catch((error) => {
        console.error(error);
      });
    },
  },
});

if (import.meta.hot) {
  import.meta.hot.accept(acceptHMRUpdate(useSidebarStore, import.meta.hot));
}
