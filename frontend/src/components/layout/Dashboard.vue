<script setup>
	import { ref, watch, onMounted } from "vue";
	import { useRoute } from "vue-router";

	import RouterNavbar from "../dashboard/app/AppNavbar.vue";
	import RouterFooter from "../dashboard/app/AppFooter.vue";
	import RouterSidebar from "../dashboard/app/AppSidebar.vue";

	import Breadcrumbs from "@/components/Breadcrumbs.vue";
	import { generateBreadcrumbs } from "@/composables/breadcrumbs.js";

	import { useSidebarStore } from "../../stores/sidebar.js";
	const sidebar = useSidebarStore();

	import { useNotificationStore } from "@/stores/notification.js";
	const notifyStore = useNotificationStore();

	const route = useRoute();
	const crumbs = ref([]);

	watch(
		() => route.fullPath,
		() => {
			crumbs.value = generateBreadcrumbs(route);
		},
		{ immediate: true }
	);

	onMounted(() => {
		notifyStore.initialize();
	});
</script>

<template>
	<div class="dashboard-layout d-flex min-vh-100 bg-body-tertiary">
		<div
			v-if="sidebar.mobileOpen"
			class="sidebar-overlay"
			@click="sidebar.closeMobile()"
		></div>
		<router-sidebar />
		<div class="dashboard-layout__content flex-grow-1 d-flex flex-column">
			<router-navbar />
			<main class="dashboard-layout__main flex-grow-1 px-3 py-4 overflow-auto">
				<div class="container-fluid">
					<div class="row g-3">
						<Breadcrumbs v-if="crumbs.length" :items="crumbs" />
						<router-view />
					</div>
				</div>
			</main>
			<router-footer />
		</div>
	</div>
</template>

<style scoped>
	.sidebar-overlay {
		position: fixed;
		inset: 0;
		background-color: rgba(0, 0, 0, 0.45);
		z-index: 1025;
	}
	.dashboard-layout__content {
		min-width: 0;
	}
</style>
