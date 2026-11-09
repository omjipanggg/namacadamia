<script setup>
	defineProps({
		variant: {
			type: String,
			default: "grow",
		},
	});
</script>

<template>
	<template v-if="variant === 'grow'">
		<div class="spinner-grow spinner-grow-sm text-primary" role="status">
			<span class="visually-hidden">Loading...</span>
		</div>
		<div class="spinner-grow spinner-grow-sm text-warning mx-1" role="status">
			<span class="visually-hidden">Loading...</span>
		</div>
		<div class="spinner-grow spinner-grow-sm text-alternative" role="status">
			<span class="visually-hidden">Loading...</span>
		</div>
	</template>
	<template v-else>
		<div class="spinner-border spinner-border-sm" role="status">
			<span class="visually-hidden">Loading...</span>
		</div>
	</template>
</template>

<style scoped>
	.text-alternative {
		--bs-text-opacity: 1;
		color: rgb(253 13 245) !important;
	}
</style>
