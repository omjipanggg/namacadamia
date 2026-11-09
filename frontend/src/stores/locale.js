import { ref } from "vue";
import { defineStore } from "pinia";

import i18n from "@/helpers/locale.js";

export const useLocaleStore = defineStore("locale", () => {
	const locale = ref(localStorage.getItem("locale") || "en");

	const setLocale = (lang) => {
		locale.value = lang;
		localStorage.setItem("locale", lang);
		i18n.global.locale.value = lang;
	};

	const toggleLocale = () => {
		setLocale(locale.value === "en" ? "id" : "en");
	};

	return {
		locale,
		setLocale,
		toggleLocale,
	};
});
