<script setup>
	import { ref, computed } from "vue";
	import { useAuthStore } from "@/stores/auth.js";
	import AppSidebarItem from "./AppSidebarItem.vue";

	const props = defineProps({
		group: {
			type: Object,
			default: () => ({}),
		},
	});

	const auth = useAuthStore();

	const activeExpandedKey = ref(null);

	const handleToggle = (key) => {
		activeExpandedKey.value = activeExpandedKey.value === key ? null : key;
	};

	const visibleItems = computed(() => {
		return (props.group.items ?? []).filter((item) => {
			if (!item.meta?.restricted) {
				return true;
			}

			if (!item.meta?.visible) {
				return false;
			}

			return auth.isAdmin;
		});
	});
</script>

<template>
	<div class="sidebar-group mt-3">
		<small class="sidebar-title text-uppercase text-secondary fw-bold d-block">
			{{ $t(props.group.label) }}
		</small>
		<AppSidebarItem
			v-for="item in visibleItems"
			:key="item.key"
			:item="item"
			:is-expanded="activeExpandedKey === item.key"
			@toggle="handleToggle(item.key)"
		/>
	</div>
</template>
