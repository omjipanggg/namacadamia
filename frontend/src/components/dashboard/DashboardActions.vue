<script setup>
	import { useToast } from "@/composables/toast.js";
	import { useModalStore } from "@/stores/modal.js";
	import { useAuthStore } from "@/stores/auth.js";
	import { useI18n } from "vue-i18n";

	const { t } = useI18n();

	const toast = useToast();

	const modalStore = useModalStore();
	const authStore = useAuthStore();

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

		window.location.href = "/";
	};
</script>

<template>
	<ul class="navbar-nav ms-auto">
		<li class="nav-item dropdown">
			<a
				class="nav-link dropdown-toggle"
				href="#"
				role="button"
				data-bs-toggle="dropdown"
				aria-expanded="false"
			>
				<i class="bi bi-person"></i>
			</a>
			<ul class="dropdown-menu dropdown-menu-end">
				<li>
					<RouterLink :to="{ name: 'dashboard.profile' }" class="dropdown-item">
						{{ $t("dashboard.profile") }}
					</RouterLink>
					<RouterLink
						:to="{ name: 'dashboard.settings' }"
						class="dropdown-item"
					>
						{{ $t("settings.title") }}
					</RouterLink>
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
	</ul>
</template>
