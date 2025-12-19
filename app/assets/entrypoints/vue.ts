import { registerPlugins } from '@/vue/plugins';

// Composables
import { createApp } from 'vue';

import App from '@/vue/App.vue';

const app = createApp(App);

registerPlugins(app);

app.mount('#app');
