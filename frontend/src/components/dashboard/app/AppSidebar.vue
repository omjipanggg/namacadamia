<script setup>
	import AppSidebarGroup from "./AppSidebarGroup.vue";
	import AppLogo from "./AppLogo.vue";
	import navigation from "@/resources/navigation.js";
	import { useSidebarStore } from "@/stores/sidebar.js";

	const sidebar = useSidebarStore();
</script>

<template>
	<aside
		id="sidebar"
		:class="[
			'sidebar',
			'sidebar-dash',
			'border-end',
			'flex-shrink-0',
			'd-flex',
			'flex-column',
			{
				collapsed: sidebar.collapsed,
				show: sidebar.mobileOpen,
			},
		]"
	>
		<AppLogo />
		<div class="px-2 pb-3 flex-grow-1 overflow-auto">
			<AppSidebarGroup
				v-for="group in navigation"
				:key="group.label"
				:group="group"
			/>
		</div>
	</aside>
</template>

<style scoped>
	.sidebar-scrollable {
		min-height: calc (100vh - 92px);
	}
</style>
