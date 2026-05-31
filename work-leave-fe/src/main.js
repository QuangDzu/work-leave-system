import { createPinia } from "pinia";
import { createApp } from "vue";
import { Toaster } from "vue-sonner";
import "vue-sonner/style.css";
import App from "./App.vue";
import router from "./router/router_index";
import "./style.css";

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.use(Toaster);

app.mount("#app");
