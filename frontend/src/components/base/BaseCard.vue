<script setup>
	defineOptions({ inheritAttrs: false });
	defineProps({
		clickable: {
			type: Boolean,
			default: false,
		},
		hover: {
			type: Boolean,
			default: true,
		},
		ribbon: {
			type: Boolean,
			default: false,
		},
	});
</script>

<template>
	<div
		class="card shadow-sm"
		:class="{
			'hover-lifted': hover,
			'cursor-pointer': clickable,
		}"
		v-bind="$attrs"
	>
		<div v-if="$slots.header" class="card-header">
			<slot name="header"></slot>
		</div>

		<div class="card-body" :class="{ 'ribbon-container': ribbon }">
			<slot></slot>
		</div>

		<div v-if="$slots.footer" class="card-footer">
			<slot name="footer"></slot>
		</div>
	</div>
</template>
