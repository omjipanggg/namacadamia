import { ref } from "vue";
import { defineStore } from "pinia";

export const useThemeStore = defineStore("theme", () => {
	const darkMode = ref(false);

	const toggleTheme = () => {
		darkMode.value = !darkMode.value;
		applyTheme();
	};

	const applyTheme = () => {
		document.documentElement.setAttribute(
			"data-bs-theme",
			darkMode.value ? "dark" : "light",
		);
		localStorage.setItem("theme", darkMode.value ? "dark" : "light");
	};

	const initialize = () => {
		darkMode.value = localStorage.getItem("theme") === "dark";
		applyTheme();
	};

	return {
		darkMode,
		initialize,
		toggleTheme,
	};
});
