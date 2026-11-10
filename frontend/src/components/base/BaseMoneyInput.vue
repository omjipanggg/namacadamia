<script setup>
	import { useId, computed } from "vue";
	import { formatNumber, cleanNumber } from "@/helpers/format.js";

	const model = defineModel({ type: [String, Number], default: "" });

	defineOptions({ inheritAttrs: false });
	defineProps({
		label: { type: String, default: "" },
		type: { type: String, default: "text" },
		error: { type: String, default: "" },
		helpText: { type: String, default: "" },
		id: { type: String, default: () => useId() },
	});

	const displayValue = computed(() => formatNumber(model.value, false));

	const onInput = (event) => {
		const rawValue = event.target.value;
		const cleaned = cleanNumber(rawValue);
		model.value = cleaned ? parseFloat(cleaned) : null;
		event.target.value = formatNumber(cleaned, false);
	};
</script>

<template>
	<div>
		<label v-if="label" :for="id" class="form-label">{{ label }}</label>
		<input
			:id="id"
			:type="type"
			class="form-control"
			:class="{ 'is-invalid': error }"
			v-bind="$attrs"
			:value="displayValue"
			@input="onInput"
		/>
		<div v-if="helpText && !error" class="form-text">{{ helpText }}</div>
		<div v-if="error" class="invalid-feedback">
			{{ $te(error) ? $t(error) : error }}
		</div>
	</div>
</template>
