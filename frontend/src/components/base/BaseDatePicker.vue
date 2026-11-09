<script setup>
	import { useId } from "vue";

	defineOptions({ inheritAttrs: false });

	const model = defineModel({ type: String, default: "" });

	defineProps({
		label: { type: String, default: "" },
		error: { type: String, default: "" },
		id: { type: String, default: () => useId() },
	});
</script>

<template>
	<div>
		<label v-if="label" :for="id" class="form-label">{{ label }}</label>
		<input
			:id="id"
			v-model="model"
			type="datetime-local"
			class="form-control"
			:class="{ 'is-invalid': error }"
			v-bind="$attrs"
		/>
		<div v-if="error" class="invalid-feedback">{{ error }}</div>
	</div>
</template>
