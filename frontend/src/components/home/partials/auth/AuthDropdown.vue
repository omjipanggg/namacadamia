<script setup>
	import { useToast } from "@/composables/toast.js";
	import { useModalStore } from "@/stores/modal.js";
	import { useAuthStore } from "@/stores/auth.js";
	import { useI18n } from "vue-i18n";
	import { computed } from "vue";

	const { t } = useI18n();

	const toast = useToast();

	const modalStore = useModalStore();
	const authStore = useAuthStore();

	const imageUrl = computed(() => {
		return authStore.user?.profile?.image_url
			? `${import.meta.env.VITE_APP_BACKEND_URL}/storage/images/${authStore.user.profile.image_url}`
			: null;
	});

	const handleLogout = async () => {
		const confirmed = await modalStore.open({
			title: t("common.confirm"),
			message: t("auth.leaving"),
			confirmText: t("common.yes"),
			cancelText: t("common.no"),
		});

		if (!confirmed) return;

		await authStore.logout();

		toast.success(t("notifications.title"), t("auth.logoutSuccess"));

		setTimeout(() => {
			window.location.href = "/";
		}, 1250);
	};
</script>

<template>
	<li v-if="!authStore.isLoggedIn" class="nav-item">
		<RouterLink :to="{ name: 'login' }" class="nav-link">{{
			$t("auth.login")
		}}</RouterLink>
	</li>
	<li v-else class="nav-item dropdown">
		<a
			class="nav-link dropdown-toggle py-0"
			href="#"
			role="button"
			data-bs-toggle="dropdown"
			aria-expanded="false"
		>
			<img
				:src="imageUrl"
				loading="lazy"
				class="img-fluid d-lg-inline-block d-none"
				alt="avatar"
				style="width: 36px; height: 36px"
			/>
			<span class="d-lg-none d-inline-block">
				{{ authStore.user.email }}
			</span>
		</a>
		<ul class="dropdown-menu dropdown-menu-end">
			<li>
				<RouterLink :to="{ name: 'dashboard.index' }" class="dropdown-item">
					<i class="bi bi-house me-2"></i>
					{{ $t("dashboard.title") }}</RouterLink
				>
			</li>

			<li class="d-lg-none d-inline">
				<a
					href="#"
					class="dropdown-item d-flex align-items-center justify-content-between"
				>
					<div class="dropdown-item-group">
						<i class="bi bi-bell me-2"></i>
						{{ $t("notifications.title") }}
					</div>
					<span class="badge bg-primary rounded-pill">2</span>
				</a>
			</li>
			<li>
				<RouterLink class="dropdown-item" :to="{ name: 'dashboard.profile' }">
					<i class="bi bi-person me-2"></i>
					{{ $t("dashboard.profile") }}</RouterLink
				>
			</li>
			<li>
				<RouterLink :to="{ name: 'dashboard.settings' }" class="dropdown-item">
					<i class="bi bi-gear me-2"></i>
					{{ $t("settings.title") }}</RouterLink
				>
			</li>
			<li><hr class="dropdown-divider" /></li>
			<li>
				<a class="dropdown-item" href="#" @click="handleLogout">
					<i class="bi bi-box-arrow-right me-2"></i>
					{{ $t("auth.logout") }}</a
				>
			</li>
		</ul>
	</li>
</template>

<style scoped>
	.dropdown-toggle::after {
		margin-left: 0.5rem !important;
	}
	.dropdown-menu {
		min-width: 222px !important;
	}
</style>
