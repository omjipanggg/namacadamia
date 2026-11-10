<script setup>
	import { computed, ref, onMounted, onBeforeUnmount } from "vue";

	const model = defineModel({
		type: Array,
		default: () => [],
	});

	const props = defineProps({
		suggestions: {
			type: Array,
			default: () => [],
		},
		attribute: {
			type: Object,
			default: () => ({}),
		},
	});

	const input = ref("");
	const isOpen = ref(false);
	const activeIndex = ref(-1);
	const container = ref(null);
	const tagInput = ref(null);

	const getLabel = (item) => item.label ?? item.title ?? "";

	const filteredSuggestions = computed(() => {
		const query = input.value.trim().toLowerCase();

		if (!query) return [];

		return props.suggestions.filter((item) => {
			const label = getLabel(item).toLowerCase();

			const alreadySelected = model.value.some(
				(selected) => getLabel(selected).toLowerCase() === label
			);

			return label.includes(query) && !alreadySelected;
		});
	});

	const resetInput = () => {
		input.value = "";
		isOpen.value = false;
		activeIndex.value = -1;
	};

	const addTag = (value = input.value) => {
		const label = String(value).trim();

		if (!label) return;

		const normalizedLabel = label.toLowerCase();

		const alreadySelected = model.value.some(
			(item) => getLabel(item).toLowerCase() === normalizedLabel
		);

		if (!alreadySelected) {
			const existing = props.suggestions.find(
				(item) => getLabel(item).toLowerCase() === normalizedLabel
			);

			model.value = [
				...model.value,
				{
					id: existing?.id ?? null,
					label,
					attribute_id:
						existing?.attribute_id ??
						props.attribute.id ??
						props.attribute.value,
					abbr: existing?.abbr ?? "",
				},
			];
		}

		resetInput();
	};

	const removeTag = (index) => {
		model.value = model.value.filter((_, i) => i !== index);
	};

	const selectSuggestion = (item) => {
		addTag(getLabel(item));
	};

	const handleKeydown = (event) => {
		if (event.key === "ArrowDown") {
			event.preventDefault();

			if (filteredSuggestions.value.length) {
				activeIndex.value = Math.min(
					activeIndex.value + 1,
					filteredSuggestions.value.length - 1
				);
			}
		}

		if (event.key === "ArrowUp") {
			event.preventDefault();
			activeIndex.value = Math.max(activeIndex.value - 1, 0);
		}

		if (event.key === "Enter" || event.key === ",") {
			event.preventDefault();

			const selected = filteredSuggestions.value[activeIndex.value];

			if (selected) {
				selectSuggestion(selected);
			} else {
				addTag();
			}
		}

		if (event.key === "Escape") {
			isOpen.value = false;
			activeIndex.value = -1;
		}

		if (event.key === "Backspace" && !input.value && model.value.length) {
			removeTag(model.value.length - 1);
		}
	};

	const handleClickOutside = (event) => {
		if (!container.value?.contains(event.target)) {
			isOpen.value = false;
		}
	};

	onMounted(() => {
		document.addEventListener("click", handleClickOutside);
	});

	onBeforeUnmount(() => {
		document.removeEventListener("click", handleClickOutside);
	});
</script>

<template>
	<div ref="container" class="position-relative">
		<div
			class="d-flex flex-wrap align-items-center gap-2 py-2"
			@click="tagInput?.focus()"
		>
			<span
				v-for="(tag, index) in model"
				:key="tag.id ?? `${tag.label}-${index}`"
				class="badge bg-primary-subtle d-inline-flex align-items-center gap-2"
			>
				{{ tag.label }}

				<button
					type="button"
					class="btn-close"
					style="font-size: 0.55rem"
					aria-label="Remove tag"
					@click.stop="removeTag(index)"
				/>
			</span>

			<input
				:id="`taggable-${attribute.label.toLowerCase()}`"
				ref="tagInput"
				v-model="input"
				type="text"
				class="form-control flex-grow-1"
				style="outline: none; min-width: 120px"
				:placeholder="
					$t('select.tagPlaceholder', {
						item: attribute.label ?? attribute.title,
					})
				"
				@focus="isOpen = true"
				@input="
					isOpen = true;
					activeIndex = -1;
				"
				@keydown="handleKeydown"
			/>
		</div>

		<div
			v-if="isOpen && filteredSuggestions.length"
			class="dropdown-menu show w-100 shadow-sm mt-1"
		>
			<button
				v-for="(suggestion, index) in filteredSuggestions"
				:key="suggestion.id"
				type="button"
				class="dropdown-item"
				:class="{ active: activeIndex === index }"
				@mousedown.prevent="selectSuggestion(suggestion)"
			>
				{{ getLabel(suggestion) }}
			</button>
		</div>

		<small
			v-if="isOpen && input.trim() && !filteredSuggestions.length"
			class="text-muted d-block mt-1"
		>
			{{ $t("common.enterToAdd", { data: input.trim() }) }}
		</small>
	</div>
</template>
