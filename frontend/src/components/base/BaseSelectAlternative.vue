<script setup>
	import { useId } from "vue";
	defineOptions({ inheritAttrs: false });

	const model = defineModel({ type: [String, Number, Object], default: "" });

	defineProps({
		label: { type: String, default: "" },
		options: {
			type: Array,
			required: true,
		},
		placeholder: { type: String, default: "" },
		error: { type: String, default: "" },
		id: { type: String, default: () => useId() },
		actionLabel: {
			type: String,
			default: "",
		},
	});
</script>

<template>
	<div ref="container" class="position-relative">
		<label v-if="label" :for="id" class="form-label">{{ label }}</label>
		<select
			:id="id"
			v-model="model"
			class="form-select"
			:class="{ 'is-invalid': error }"
			v-bind="$attrs"
		>
			<option value="" disabled>{{ placeholder }}</option>
			<option
				v-for="opt in options"
				:key="typeof opt === 'object' ? opt.value : opt"
				:value="typeof opt === 'object' ? opt.value : opt"
			>
				{{ typeof opt === "object" ? opt.title : opt }}
			</option>
		</select>
		<div v-if="error" class="invalid-feedback">{{ error }}</div>
	</div>
</template>
