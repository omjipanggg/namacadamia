<script setup>
	import { computed } from "vue";

	import BaseMultiSelect from "@/components/base/BaseMultiSelect.vue";
	import BaseTagInput from "@/components/base/BaseTagInput.vue";

	const selectedAttributes = defineModel({
		type: Array,
		default: () => [],
	});

	const props = defineProps({
		lookup: {
			type: Object,
			default: () => ({}),
		},
	});

	const attributeSelection = computed({
		get() {
			return selectedAttributes.value.map((group) => ({
				label: group.attribute.label,
				value: group.attribute.id,
			}));
		},

		set(options) {
			const currentGroups = selectedAttributes.value;

			selectedAttributes.value = options.map((option) => {
				const existing = currentGroups.find(
					(group) => String(group.attribute.id) === String(option.value)
				);

				return (
					existing ?? {
						attribute: {
							id: option.value,
							label: option.label,
						},
						values: [],
					}
				);
			});
		},
	});

	const availableOptions = computed(() => {
		const selectedIds = new Set(
			selectedAttributes.value.map((group) => String(group.attribute.id))
		);

		return (props.lookup.product_attributes ?? []).filter(
			(option) => !selectedIds.has(String(option.value))
		);
	});

	const getAttributeSuggestions = (attributeId) => {
		return (props.lookup.product_attribute_values ?? [])
			.filter((item) => item.meta?.product_attribute_id === attributeId)
			.map((item) => ({
				id: item.value,
				label: item.title,
				attribute_id: attributeId,
				abbr: item.meta?.abbr ?? null,
			}));
	};
</script>

<template>
	<div class="card">
		<div class="card-header">
			<p class="fw-semibold m-0">
				{{ $t("products.variants") }}
			</p>
		</div>

		<div class="card-body">
			<div class="row g-3">
				<div class="col-12">
					<BaseMultiSelect
						v-model="attributeSelection"
						:form-label="$t('products.attributes')"
						:options="availableOptions"
						:placeholder="$t('select.one')"
						:close-on-select="true"
						mode="tags"
					/>
				</div>

				<div v-if="selectedAttributes.length" class="col-12">
					<div class="d-flex flex-wrap gap-3 align-items-stretch">
						<div
							v-for="group in selectedAttributes"
							:key="group.attribute.id"
							class="border flex-grow-1 rounded py-2 px-3"
						>
							<p class="fw-semibold mb-0 pb-2 border-bottom">
								{{ group.attribute.label }}
							</p>

							<BaseTagInput
								v-model="group.values"
								:attribute="group.attribute"
								:suggestions="getAttributeSuggestions(group.attribute.id)"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
