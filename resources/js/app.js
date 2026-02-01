import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

const app = createApp(App);

// ambil nama dari session blade
const el = document.getElementById("app");
app.config.globalProperties.$nama = el.dataset.nama ?? "";

app.use(router).mount("#app");
