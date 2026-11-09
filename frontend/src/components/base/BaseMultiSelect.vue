<script setup>
	import Multiselect from "@vueform/multiselect";
	import { useId, computed } from "vue";

	defineOptions({ inheritAttrs: false });

	const model = defineModel({
		type: [String, Number, Object, Array],
		default: "",
	});

	const props = defineProps({
		formLabel: { type: String, default: "" },
		options: { type: Array, required: true },
		placeholder: { type: String, default: "" },
		id: { type: String, default: () => useId() },
		error: { type: String, default: "" },
		mode: { type: String, default: "" },
		closeOnSelect: { type: Boolean, default: true },
		openDirection: { type: String, default: "bottom" },
		searchable: { type: Boolean, default: false },
		object: { type: Boolean, default: true },
	});

	const transformedOptions = computed(() => {
		return props.options.map((item) => ({
			label: item.title,
			value: item.value,
		}));
	});
</script>

<template>
	<div>
		<span
			v-if="formLabel"
			:id="`${props.id}-assist`"
			class="form-label mb-2 d-block"
			>{{ formLabel }}</span
		>
		<Multiselect
			:id="props.id"
			v-model="model"
			:options="transformedOptions"
			:mode="props.mode"
			:object="props.object"
			:placeholder="props.placeholder"
			:searchable="props.searchable"
			:close-on-select="props.closeOnSelect"
			:open-direction="props.openDirection"
			:class="{ 'is-invalid': error }"
			v-bind="$attrs"
		>
			<template #clear="{ clear }">
				<span
					class="multiselect-clear"
					tabindex="-1"
					@mousedown.prevent="clear"
				>
					<span class="multiselect-clear-icon"></span>
				</span>
			</template>
		</Multiselect>
		<div v-if="error" class="invalid-feedback">{{ $t(error) }}</div>
	</div>
</template>

<style scoped>
	.is-invalid {
		border-color: var(--bs-form-invalid-border-color);
		padding-right: calc(1.5em + 0.75rem);
		background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
		background-repeat: no-repeat;
		background-position: right calc(0.375em + 0.1875rem) center;
		background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
	}
</style>
