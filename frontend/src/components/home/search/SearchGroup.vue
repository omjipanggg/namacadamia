<script setup>
	defineProps({
		title: {
			type: String,
			required: true,
		},
		items: {
			type: Array,
			required: true,
		},
	});
</script>

<template>
	<div v-if="items.length">
		<div
			class="px-3 py-2 text-uppercase fw-semibold text-secondary small border-bottom"
		>
			{{ $t(`${title}.title`) }}
		</div>

		<slot></slot>
	</div>
</template>
