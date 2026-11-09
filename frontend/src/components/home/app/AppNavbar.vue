<script setup>
	import { useLocaleStore } from "@/stores/locale.js";
	import { useOutlineStore } from "@/stores/outline.js";
	import { useThemeStore } from "@/stores/theme.js";
	import { useI18n } from "vue-i18n";

	const localeStore = useLocaleStore();
	const outlineStore = useOutlineStore();
	const themeStore = useThemeStore();

	const appName = import.meta.env.VITE_APP_TITLE;

	import { useToast } from "@/composables/toast.js";

	import NotificationDropdown from "@/components/home/partials/notification/NotificationDropdown.vue";
	import AuthDropdown from "@/components/home/partials/auth/AuthDropdown.vue";

	const toast = useToast();
	const { t } = useI18n();

	const switchLocale = () => {
		localeStore.toggleLocale();
		toast.success(t("notifications.title"), t("locale.switched"));
	};

	const handleMaintenance = () => {
		toast.danger(t("notifications.title"), t("errors.access.maintenance"));
	};
</script>

<template>
	<nav
		class="navbar navbar-expand-lg bg-body-tertiary"
		:aria-label="`${appName} default navigation`"
	>
		<div class="container-lg">
			<ul class="navbar-nav d-none d-lg-inline-block">
				<li class="nav-item dropdown">
					<a
						class="nav-link dropdown-toggle no-caret"
						href="#"
						role="button"
						data-bs-toggle="dropdown"
						aria-expanded="false"
					>
						<i class="bi me-2 bi-three-dots-vertical"></i>
					</a>
					<ul class="dropdown-menu">
						<li>
							<a
								class="dropdown-item"
								href="#"
								@click="outlineStore.toggleOutline"
							>
								<i class="bi bi-terminal me-2"></i>
								{{
									outlineStore.outline
										? $t("settings.hideOutlines")
										: $t("settings.showOutlines")
								}}</a
							>
						</li>
						<li>
							<a class="dropdown-item" href="#" @click="themeStore.toggleTheme">
								<i v-if="themeStore.darkMode" class="bi me-2 bi-cloud-sun"></i>
								<i v-else class="bi me-2 bi-cloud-moon"></i>
								{{
									$t(
										themeStore.darkMode
											? "settings.toggleLight"
											: "settings.toggleDark"
									)
								}}
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="#" @click="switchLocale">
								<i class="bi bi-translate me-2"></i>
								{{ $t("locale.switch") }}</a
							>
						</li>
					</ul>
				</li>
			</ul>
			<router-link class="navbar-brand me-auto" :to="{ name: 'home' }">{{
				appName
			}}</router-link>
			<button
				class="navbar-toggler"
				type="button"
				data-bs-toggle="offcanvas"
				data-bs-target="#offcanvasNavbar2"
				aria-controls="offcanvasNavbar2"
				aria-label="Toggle navigation"
			>
				<span class="navbar-toggler-icon"></span>
			</button>
			<div
				id="offcanvasNavbar2"
				class="offcanvas offcanvas-end"
				tabindex="-1"
				aria-labelledby="offcanvasNavbar2Label"
			>
				<div class="offcanvas-header border-bottom">
					<h5 id="offcanvasNavbar2Label" class="offcanvas-title">
						{{ appName }}
					</h5>
					<button
						type="button"
						class="btn-close"
						data-bs-dismiss="offcanvas"
						aria-label="Close"
					></button>
				</div>
				<div class="offcanvas-body">
					<ul class="navbar-nav me-auto ms-0 ms-lg-3">
						<li class="nav-item">
							<router-link :to="{ name: 'blog.index' }" class="nav-link">
								{{ $t("blog.title") }}
							</router-link>
						</li>
						<li class="nav-item">
							<router-link :to="{ name: 'products.index' }" class="nav-link">
								{{ $t("products.title") }}
							</router-link>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link" @click="handleMaintenance">
								{{ $t("navigation.services") }}
							</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link" @click="handleMaintenance">
								{{ $t("navigation.contact") }}
							</a>
						</li>
					</ul>
					<ul class="navbar-nav justify-content-end flex-grow-1 pe-0">
						<li class="d-lg-inline-block d-none nav-item">
							<router-link
								:to="{ name: 'cart.index' }"
								class="nav-link position-relative"
							>
								<i class="bi bi-cart"></i>
								<span
									v-if="false"
									class="position-absolute translate-middle badge rounded-pill bg-danger"
									style="top: 25%; font-size: 10px"
								>
									2
									<span class="visually-hidden">{{ $t("cart.items") }}</span>
								</span>
							</router-link>
						</li>
						<NotificationDropdown />
						<li class="nav-item d-lg-none d-inline">
							<router-link :to="{ name: 'cart.index' }" class="nav-link">
								<div class="d-flex align-items-center justify-content-between">
									<span>{{ $t("cart.title") }}</span>
									<span v-if="false" class="badge bg-primary rounded-pill"
										>2</span
									>
								</div>
							</router-link>
						</li>
						<li class="nav-item dropdown d-lg-none d-inline-block">
							<a
								class="nav-link dropdown-toggle"
								href="#"
								role="button"
								data-bs-toggle="dropdown"
								aria-expanded="false"
							>
								{{ $t("settings.configurations") }}
							</a>
							<ul class="dropdown-menu">
								<li>
									<a
										class="dropdown-item"
										href="#"
										@click="themeStore.toggleTheme"
									>
										<i
											v-if="themeStore.darkMode"
											class="bi me-2 bi-cloud-sun"
										></i>
										<i v-else class="bi me-2 bi-cloud-moon"></i>
										{{
											$t(
												themeStore.darkMode
													? "settings.toggleLight"
													: "settings.toggleDark"
											)
										}}
									</a>
								</li>
								<li v-if="false">
									<a
										class="dropdown-item"
										href="#"
										@click="outlineStore.toggleOutline"
									>
										<i class="bi bi-terminal me-2"></i>
										{{
											outlineStore.outline
												? $t("settings.hideOutlines")
												: $t("settings.showOutlines")
										}}</a
									>
								</li>
								<li>
									<a class="dropdown-item" href="#" @click="switchLocale">
										<i class="bi bi-translate me-2"></i>
										{{ $t("locale.switch") }}</a
									>
								</li>
							</ul>
						</li>
						<AuthDropdown />
					</ul>
				</div>
			</div>
		</div>
	</nav>
</template>
