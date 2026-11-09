<script setup>
	import { useId } from "vue";

	defineOptions({ inheritAttrs: false });

	const model = defineModel({ type: [String, Number], default: "" });

	defineProps({
		label: { type: String, default: "" },
		type: { type: String, default: "text" },
		error: { type: String, default: "" },
		helpText: { type: String, default: "" },
		plainText: { type: Boolean, default: false },
		id: { type: String, default: () => useId() },
	});
</script>

<template>
	<div>
		<label v-if="label" :for="id" class="form-label">{{ label }}</label>
		<input
			:id="id"
			v-model="model"
			:type="type"
			:class="[
				{ 'is-invalid': error },
				plainText ? 'form-control-plaintext' : 'form-control',
			]"
			v-bind="$attrs"
		/>
		<div v-if="helpText && !error" class="form-text">{{ helpText }}</div>
		<div v-if="error" class="invalid-feedback">
			{{ $te(error) ? $t(error) : error }}
		</div>
	</div>
</template>
