<script setup>
import { useSidebarStore } from "@/stores/sidebar.js";
import { useBreakpoints, breakpointsBootstrapV5 } from "@vueuse/core";

const appName = import.meta.env.VITE_APP_TITLE;
const sidebar = useSidebarStore();

const breakpoints = useBreakpoints(breakpointsBootstrapV5);
const isMobile = breakpoints.smaller("md");
</script>

<template>
	<div
		class="sidebar-logo d-flex align-items-center justify-content-between px-3 text-start border-bottom position-relative"
	>
		<RouterLink
			:to="{ name: 'home' }"
			class="text-decoration-none text-reset"
		>
			<h3 class="fw-bold mb-0">{{ appName }}</h3>
			<small class="text-secondary">{{ $t("dashboard.subtitle") }}</small>
		</RouterLink>
		<button
			class="btn btn-close"
			@click="sidebar.toggle()"
			v-if="isMobile"
		></button>
	</div>
</template>
