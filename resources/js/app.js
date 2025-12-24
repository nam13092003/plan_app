import './bootstrap';
import '../css/app.css';
import 'frappe-gantt/dist/frappe-gantt.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';
import axios from 'axios';

const initializeApp = async () => {
    // Ensure CSRF cookie is set before making any other API calls
    await axios.get('/sanctum/csrf-cookie');

    const app = createApp(App);

    app.use(createPinia());
    app.use(router);

    app.mount('#app');
};

initializeApp();

