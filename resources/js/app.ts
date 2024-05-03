import '@/scss/style.scss';
import '@mdi/font/css/materialdesignicons.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';

import App from './App.vue';
import router from './router';

// Vuetify
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';

const Vuetify = createVuetify({
    components,
    directives,
});

// Create App
const app = createApp(App);

app.use(createPinia())
app.use(router);
app.use(Vuetify);

app.mount('#app');
