<script setup>
	import { watch } from "vue";
	import { useI18n } from "vue-i18n";
	import { useRoute } from "vue-router";
	import { useToast } from "@/composables/toast.js";
	import { useModalStore } from "@/stores/modal.js";
	import { useSidebarStore } from "@/stores/sidebar.js";
	import { useAuthStore } from "@/stores/auth.js";
	import { useNotificationStore } from "@/stores/notification.js";
	import { useBreakpoints, breakpointsBootstrapV5 } from "@vueuse/core";

	const { t } = useI18n();

	const props = defineProps({
		item: {
			type: Object,
			default: () => ({}),
		},
		isExpanded: {
			type: Boolean,
			default: false,
		},
	});

	const emit = defineEmits(["toggle"]);

	const route = useRoute();
	const notifyStore = useNotificationStore();
	const authStore = useAuthStore();
	const toast = useToast();
	const modalStore = useModalStore();
	const sidebar = useSidebarStore();
	const breakpoints = useBreakpoints(breakpointsBootstrapV5);
	const isMobile = breakpoints.smaller("md");

	const handleAction = async (action) => {
		if (action === "logout") {
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
		}
	};

	watch(
		() => route.name,
		(newRoute) => {
			const isChildActive = props.item.children?.some(
				(child) => child.route === newRoute
			);

			if (isChildActive && !props.isExpanded) {
				emit("toggle");
			}
		},
		{ immediate: true }
	);
</script>

<template>
	<div v-if="item.children && item.children.length" class="sidebar-item-parent">
		<button
			type="button"
			class="sidebar-link w-100 border-0 bg-transparent text-start d-flex justify-content-between align-items-center"
			@click="emit('toggle')"
		>
			<div class="group">
				<i :class="['bi', 'me-1', `bi-${item.icon}`]"></i>
				{{ $t(item.title) }}
			</div>
			<i
				:class="[
					'bi',
					isExpanded ? 'bi-chevron-up' : 'bi-chevron-down',
					'ms-auto',
					'small',
				]"
			></i>
		</button>

		<Transition name="sub-menu">
			<div v-show="isExpanded" class="sub-menu">
				<RouterLink
					v-for="child in item.children"
					:key="child.key"
					:to="{ name: child.route }"
					class="sidebar-link sub-link d-block text-decoration-none"
					active-class="active"
					exact-active-class="exact-active"
					@click="isMobile && sidebar.toggle()"
				>
					<i class="bi bi-dot me-1"></i>
					{{ $t(child.title) }}
				</RouterLink>
			</div>
		</Transition>
	</div>

	<RouterLink
		v-if="item.route"
		:to="{ name: item.route }"
		class="sidebar-link position-relative"
		active-class="active"
		exact-active-class="exact-active"
		@click="isMobile && sidebar.toggle()"
	>
		<div class="group">
			<i :class="['bi', `bi-${item.icon}`, 'me-1']"></i>
			{{ $t(item.title) }}
		</div>

		<span
			v-if="notifyStore.unreadCount > 0 && item.key === 'notifications'"
			class="position-absolute badge bg-danger"
			style="right: 8px"
		>
			{{ notifyStore.unreadCount }}
			<span class="visually-hidden">{{ $t("notifications.unread") }}</span>
		</span>
	</RouterLink>

	<a
		v-else-if="item.action"
		href="#"
		class="sidebar-link position-relative"
		:class="{ 'text-danger': item.action === 'logout' }"
		@click="handleAction(item.action)"
	>
		<div class="group">
			<i :class="['bi', `bi-${item.icon}`, 'me-1']"></i>
			{{ $t(item.title) }}
		</div>
	</a>
</template>

<style scoped>
	.sub-link {
		opacity: 0.7;
	}
	.sub-link.active {
		font-weight: 600;
		opacity: 1;
	}
	.sub-menu-enter-active,
	.sub-menu-leave-active {
		transition:
			max-height 360ms ease-in-out,
			opacity 360ms ease-in-out;
		overflow: hidden;
	}

	.sub-menu-enter-from,
	.sub-menu-leave-to {
		max-height: 0;
		opacity: 0;
	}

	.sub-menu-enter-to,
	.sub-menu-leave-from {
		max-height: 240px;
		opacity: 1;
	}
</style>
