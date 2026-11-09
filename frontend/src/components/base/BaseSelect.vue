<script setup>
	import { useId, computed, onBeforeUnmount, onMounted, ref } from "vue";

	defineOptions({ inheritAttrs: false });

	const model = defineModel({
		type: [String, Number, Object, null],
		default: null,
	});

	const props = defineProps({
		label: { type: String, default: "" },
		disabled: { type: Boolean, default: false },
		options: {
			type: Array,
			required: true,
		},
		placeholder: { type: String, default: "" },
		error: { type: String, default: "" },
		id: { type: String, default: () => useId() },
		showSubtitle: {
			type: Boolean,
			default: true,
		},
		actionLabel: {
			type: String,
			default: "",
		},
	});

	const emit = defineEmits(["action"]);

	const isOpen = ref(false);
	const container = ref(null);

	const selectedOption = computed(() => {
		return props.options.find((option) => option.value === model.value);
	});

	const selectOption = (option) => {
		model.value = option.value;
		isOpen.value = false;
	};

	const handleAction = () => {
		isOpen.value = true;
		emit("action");
	};

	const handleClickOutside = (event) => {
		if (container.value && !container.value.contains(event.target)) {
			isOpen.value = false;
		}
	};

	onMounted(() => {
		document.addEventListener("click", handleClickOutside, true);
	});
	onBeforeUnmount(() => {
		document.removeEventListener("click", handleClickOutside, true);
	});
</script>

<template>
	<div ref="container" class="position-relative">
		<label v-if="label" :for="id" class="form-label">{{ label }}</label>
		<button
			:id="id"
			type="button"
			class="form-select text-start"
			:class="{ 'is-invalid': error }"
			:disabled="disabled"
			v-bind="$attrs"
			@click.stop="!disabled && (isOpen = !isOpen)"
		>
			<template v-if="selectedOption">
				{{ selectedOption.title }}
			</template>

			<span v-else class="text-muted">
				{{ placeholder }}
			</span>
		</button>
		<div v-if="isOpen" class="dropdown-menu rounded-0 show w-100 p-0">
			<div class="dropdown-options">
				<button
					v-for="option in options"
					:key="option.value"
					type="button"
					class="dropdown-item"
					@click="selectOption(option)"
				>
					<div>
						{{ option.title }}
					</div>
					<small v-if="option.subtitle && showSubtitle" class="text-muted">
						{{ option.subtitle }}
					</small>
				</button>
				<div v-if="options.length === 0" class="px-3 py-2 text-muted">
					{{ $t("select.noOptions") }}
				</div>
			</div>
			<div v-if="actionLabel" class="border-top py-1">
				<button type="button" class="dropdown-item" @click="handleAction">
					<i class="bi bi-plus-square me-2"></i>
					{{ actionLabel }}
				</button>
			</div>
		</div>
		<div v-if="error" class="invalid-feedback">
			{{ $te(error) ? $t(error) : error }}
		</div>
	</div>
</template>

<style scoped>
	.dropdown-options {
		overflow-y: auto;
		max-height: 224px;
	}
</style>
