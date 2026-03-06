import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from '@/App.vue';
import router from '@/routes';

// If you have a Tailwind CSS file, import it here!
// import '../css/app.css';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

app.mount('#app');
