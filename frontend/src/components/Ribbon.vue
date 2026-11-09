<script setup>
defineProps({
	variant: {
		type: String,
		default: "warning",
	},
	icon: {
		type: String,
		default: "percent",
	},
});
</script>
<template>
	<div class="ribbon" :class="`bg-${variant}`">
		<i class="bi" :class="`bi-${icon}`"></i>
	</div>
</template>
