<script setup>
	defineOptions({ inheritAttrs: false });

	defineProps({
		variant: {
			type: String,
			default: "success",
		},
	});
</script>

<template>
	<small
		class="d-inline-flex m-0 px-2 py-1 border rounded"
		:class="[
			`text-${variant}-emphasis`,
			`bg-${variant}-subtle`,
			`border-${variant}-subtle`,
		]"
		v-bind="$attrs"
	>
		<slot></slot>
	</small>
</template>
