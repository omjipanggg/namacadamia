<script setup>
	import { useId } from "vue";

	defineOptions({ inheritAttrs: false });

	const model = defineModel({ type: String, default: "" });

	defineProps({
		label: { type: String, default: "" },
		rows: { type: [String, Number], default: 3 },
		placeholder: { type: String, default: "" },
		error: { type: String, default: "" },
		id: { type: String, default: () => useId() },
	});
</script>

<template>
	<div>
		<label v-if="label" :for="id" class="form-label">{{ label }}</label>
		<textarea
			:id="id"
			v-model="model"
			:rows="rows"
			class="form-control"
			:class="{ 'is-invalid': error }"
			v-bind="$attrs"
			:placeholder="placeholder"
		></textarea>
		<div v-if="error" class="invalid-feedback">
			{{ $te(error) ? $t(error) : error }}
		</div>
	</div>
</template>
