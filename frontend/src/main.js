import { createApp } from "vue";

import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";

const app = createApp(App);

import { createPinia } from "pinia";
const pinia = createPinia();

import { useThemeStore } from "./stores/theme.js";
import { useLocaleStore } from "./stores/locale.js";

import router from "./router";
import i18n from "./helpers/locale.js";
import { Tooltip } from "bootstrap";

import App from "./App.vue";

app.directive("tooltip", {
	mounted(el) {
		el.tooltipInstance = new Tooltip(el);
	},
	unmounted(el) {
		if (el.tooltipInstance) {
			el.tooltipInstance.dispose();
		}
	},
});

import "@vueform/multiselect/themes/default.css";

app.use(pinia);

const localeStore = useLocaleStore();
localeStore.setLocale(localeStore.locale);

const themeStore = useThemeStore(pinia);
themeStore.initialize();

app.use(router);
app.use(i18n);
app.mount("#app");
