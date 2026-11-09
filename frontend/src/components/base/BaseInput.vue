<script setup>
	import { useId, computed, ref } from "vue";

	defineOptions({ inheritAttrs: false });

	const model = defineModel({ type: [String, Number], default: "" });

	const props = defineProps({
		label: { type: String, default: "" },
		type: { type: String, default: "text" },
		error: { type: String, default: "" },
		helpText: { type: String, default: "" },
		plainText: { type: Boolean, default: false },
		id: { type: String, default: () => useId() },
	});

	const isReadable = ref(false);

	const currentType = computed(() => {
		if (props.type === "password") {
			return isReadable.value ? "text" : "password";
		}
		return props.type;
	});

	const toggle = () => {
		isReadable.value = !isReadable.value;
	};
</script>

<template>
	<div>
		<label v-if="label" :for="id" class="form-label">{{ label }}</label>
		<div :class="{ 'input-group': type === 'password' }">
			<input
				:id="id"
				v-model="model"
				:type="currentType"
				:class="[
					{ 'is-invalid': error },
					// { 'border border-secondary': type === 'password' },
					plainText ? 'form-control-plaintext' : 'form-control',
				]"
				v-bind="$attrs"
			/>

			<button
				v-if="type === 'password'"
				class="btn"
				:class="[
					{ 'is-invalid': error },
					error ? 'btn-outline-danger' : 'btn-outline-secondary',
				]"
				type="button"
				:aria-label="isReadable ? 'Hide password' : 'Show password'"
				@click.stop="toggle"
			>
				<template v-if="isReadable">
					<i class="bi bi-eye-slash-fill"></i>
				</template>
				<template v-else>
					<i class="bi bi-eye-fill"></i>
				</template>
			</button>
		</div>

		<div v-if="helpText && !error" class="form-text">{{ helpText }}</div>
		<div v-if="error" class="invalid-feedback d-block fw-semibold">
			{{ $te(error) ? $t(error) : error }}
		</div>
	</div>
</template>
