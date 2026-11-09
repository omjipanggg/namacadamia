<script setup>
	import { computed } from "vue";
	import { useNotificationStore } from "@/stores/notification.js";
	import Item from "./NotificationItem.vue";

	const notifyStore = useNotificationStore();
	const unreadCount = computed(() => notifyStore.unreadCount);

	const markAllAsRead = () => {
		alert("All notifications marked as read");
	};
</script>

<template>
	<li
		v-if="notifyStore.initialized"
		class="d-lg-inline-block d-none nav-item dropdown me-3"
	>
		<a
			href="#"
			class="nav-link position-relative dropdown-toggle no-caret"
			role="button"
			data-bs-toggle="dropdown"
			aria-expanded="false"
			aria-haspopup="true"
		>
			<i class="bi bi-bell"></i>
			<span
				v-if="unreadCount"
				class="position-absolute translate-middle badge rounded-pill bg-danger"
				style="top: 25%; font-size: 10px"
			>
				{{ unreadCount > 99 ? "99+" : unreadCount }}
				<span class="visually-hidden">{{ $t("notifications.title") }}</span>
			</span>
		</a>
		<ul class="dropdown-menu dropdown-menu-end menu-drop">
			<div class="menu-drop-header border-bottom px-3 pb-2">
				<div class="d-flex align-items-center justify-content-between">
					<div>
						{{ $t("notifications.title") }}
					</div>
					<div v-if="!notifyStore.preview" class="icon-delete">
						<a href="#" class="icon" @click="markAllAsRead">
							<i class="bi bi-trash"></i>
						</a>
					</div>
				</div>
			</div>
			<div class="menu-drop-content">
				<Item :notifications="notifyStore.preview" />
			</div>
			<!-- // notifyStore.hasItem? -->
			<div class="menu-drop-footer pt-2 px-3">
				<RouterLink
					:to="{ name: 'dashboard.notifications' }"
					class="small text-decoration-none"
				>
					<div class="d-flex align-items-center justify-content-between">
						<div>
							{{ $t("notifications.seeAll") }}
						</div>
						<i class="bi bi-chevron-right ms-2"></i>
					</div>
				</RouterLink>
			</div>
		</ul>
	</li>
</template>

<style scoped>
	.menu-drop {
		left: -184px !important;
		width: 300px;
	}

	.menu-drop a {
		color: var(--bs-primary-color);
	}

	.menu-drop .text-truncate {
		font-size: 12px;
	}

	.menu-drop .time-ago {
		font-size: 10px !important;
	}
</style>
