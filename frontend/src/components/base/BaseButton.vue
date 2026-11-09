<script setup>
	defineProps({
		variant: {
			type: String,
			default: "primary",
		},
		type: {
			type: String,
			default: "button",
		},
		loading: {
			type: Boolean,
			default: false,
		},
		disabled: {
			type: Boolean,
			default: false,
		},
		size: {
			type: String,
			default: "md",
		},
	});
</script>

<template>
	<button
		:class="['btn', `btn-${variant}`, `btn-${size}`]"
		:type="type"
		:disabled="loading || disabled"
	>
		<span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
		<slot></slot>
	</button>
</template>
