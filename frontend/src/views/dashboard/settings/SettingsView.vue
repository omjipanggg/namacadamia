<script setup>
	import { useLocaleStore } from "@/stores/locale.js";
	import { useThemeStore } from "@/stores/theme.js";
	import { useToast } from "@/composables/toast.js";

	const localeStore = useLocaleStore();
	const themeStore = useThemeStore();
	const toast = useToast();

	const switchLocale = (locale) => {
		localeStore.setLocale(locale);
		toast.success("notifications.title", "locale.switched");
	};
</script>

<template>
	<div class="col-xl-4 col-lg-6">
		<h2 class="mb-4">{{ $t("dashboard.settings") }}</h2>

		<div class="settings-group">
			<div class="settings-item">
				<h6 class="mb-2">{{ $t("settings.language") }}</h6>
				<select
					id="selectLocale"
					class="form-select"
					:value="localeStore.locale"
					@change="(e) => switchLocale(e.target.value)"
				>
					<!-- @change="(e) => localeStore.setLocale(e.target.value)" -->
					<option value="en">English</option>
					<option value="id">Bahasa Indonesia</option>
				</select>
			</div>
			<div class="settings-item">
				<h6 class="mb-2">{{ $t("parameters.title") }}</h6>
				<router-link
					class="text-decoration-dotted"
					:to="{ name: 'dashboard.parameters' }"
				>
					{{ $t("navigation.clickToContinue") }}
				</router-link>
			</div>
			<div class="settings-item">
				<h6 class="mb-2">{{ $t("settings.theme") }}</h6>
				<div class="form-check form-switch">
					<input
						id="switchCheckDarkMode"
						class="form-check-input"
						type="checkbox"
						role="switch"
						name="switchCheckDarkMode"
						:checked="themeStore.darkMode"
						@change="themeStore.toggleTheme"
					/>
					<label class="form-check-label ms-2" for="switchCheckDarkMode">{{
						$t("settings.darkMode")
					}}</label>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
	.settings-item {
		margin-top: 1.5rem;
	}
</style>
