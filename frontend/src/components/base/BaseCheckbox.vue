<script setup>
	import { useId } from "vue";

	defineOptions({ inheritAttrs: false });

	const model = defineModel({ type: [Boolean, Array], default: false });

	defineProps({
		label: { type: String, required: true },
		value: { type: [String, Number, Boolean], default: true },
		error: { type: String, default: "" },
		id: { type: String, default: () => useId() },
	});
</script>

<template>
	<div>
		<div class="form-check">
			<input
				:id="id"
				v-model="model"
				type="checkbox"
				:value="value"
				class="form-check-input"
				:class="{ 'is-invalid': error }"
				v-bind="$attrs"
			/>
			<label :for="id" class="form-check-label">{{ label }}</label>
		</div>
		<div v-if="error" class="invalid-feedback">{{ error }}</div>
	</div>
</template>
