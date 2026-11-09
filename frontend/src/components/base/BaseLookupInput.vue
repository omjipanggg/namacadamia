<script setup>
	import { onMounted, onBeforeUnmount, useId, ref, computed, watch } from "vue";

	defineOptions({ inheritAttrs: false });

	const props = defineProps({
		items: { type: Array, default: () => [] },
		placeholder: { type: String, default: "Select..." },
		taggable: { type: Object, default: () => ({}) },
		disabled: { type: Boolean, default: false },
		label: { type: String, default: "" },
		type: { type: String, default: "text" },
		error: { type: String, default: "" },
		helpText: { type: String, default: "" },
		id: { type: String, default: () => useId() },
		showSubtitle: { type: Boolean, default: true },
		actionLabel: { type: String, default: "" },
	});

	const model = defineModel({
		type: [String, Number, null],
		default: null,
	});

	const emit = defineEmits(["action"]);

	const container = ref(null);
	const opened = ref(false);
	const query = ref(null);

	const selectOption = (option) => {
		model.value = option.value;
		query.value = option.title;
		opened.value = false;
	};

	const toggle = () => {
		if (props.disabled) return;
		opened.value = !opened.value;
	};

	const handleAction = () => {
		opened.value = true;
		emit("action");
	};

	const filteredOptions = computed(() => {
		if (!query.value) {
			return props.items;
		}

		const search = query.value.toLowerCase();

		return props.items.filter(
			(item) =>
				item.title.toLowerCase().includes(search) ||
				item.subtitle?.toLowerCase().includes(search)
		);
	});

	const handleClickOutside = (event) => {
		if (container.value && !container.value.contains(event.target)) {
			opened.value = false;
		}
	};

	watch(
		[model, () => props.items],
		([newValue, items]) => {
			if (!newValue) {
				query.value = null;
				return;
			}

			const selected = items.find(
				(item) => String(item.value) === String(newValue)
			);

			if (selected) {
				query.value = selected.title;
			}
		},
		{
			immediate: true,
			deep: true,
		}
	);

	onMounted(() => {
		document.addEventListener("click", handleClickOutside, true);
		document.querySelector(`input#${props.id}`).addEventListener(
			"focus",
			(e) => {
				setTimeout(() => {
					e.target.setSelectionRange(0, 9999);
				}, 0);
			},
			true
		);
	});

	onBeforeUnmount(() => {
		document.removeEventListener("click", handleClickOutside, true);
		document.querySelector(`input#${props.id}`).removeEventListener(
			"focus",
			(e) => {
				setTimeout(() => {
					e.target.setSelectionRange(0, 9999);
				}, 0);
			},
			true
		);
	});
</script>

<template>
	<div ref="container" class="lookup-input">
		<label v-if="label" :for="id" class="form-label">{{ label }}</label>
		<input id="hiddenModel" v-model="model" type="hidden" />
		<input
			:id="id"
			v-model="query"
			:type="type"
			:class="[
				'form-control',
				'text-truncate',
				'form-grow-1',
				'cursor-pointer',
			]"
			:placeholder="placeholder"
			:disabled="disabled"
			:title="model"
			v-bind="$attrs"
			:autocomplete="false"
			@click="toggle"
		/>

		<div v-if="opened" class="dropdown-menu rounded-0 show w-100 p-0">
			<div class="dropdown-options">
				<button
					v-for="option in filteredOptions"
					:key="option.value || option"
					type="button"
					class="dropdown-item"
					@click="selectOption(option)"
				>
					<div>
						{{ option.title || option }}
					</div>
					<small v-if="option.subtitle && showSubtitle" class="text-muted">
						{{ option.subtitle || option }}
					</small>
				</button>
				<div v-if="filteredOptions.length === 0" class="px-3 py-2 text-muted">
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

		<div v-if="helpText && !error" class="form-text">{{ helpText }}</div>
		<div v-if="error" class="invalid-feedback">{{ $t(error) }}</div>
	</div>
</template>

<style scoped>
	.lookup-input {
		position: relative;
	}

	.dropdown-options {
		overflow-y: auto;
		max-height: 280px;
	}

	.form-grow-1 {
		overflow: hidden;
		text-overflow: ellipsis;
		min-width: 270px;
		/* max-width: 270px; */
		width: 100%;
	}
</style>
