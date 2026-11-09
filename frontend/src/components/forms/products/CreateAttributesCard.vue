<script setup>
	import { computed } from "vue";
	import BaseMultiSelect from "@/components/base/BaseMultiSelect.vue";
	import BaseCheckbox from "@/components/base/BaseCheckbox.vue";

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
			return selectedAttributes.value.map((item) => item.attribute);
		},

		set(attributes) {
			selectedAttributes.value = attributes.map((attribute) => {
				const existing = selectedAttributes.value.find(
					(item) => item.attribute.value === attribute.value
				);

				return (
					existing ?? {
						attribute,
						valueIds: [],
					}
				);
			});
		},
	});

	const getAttributeValues = (id) => {
		return props.lookup.product_attribute_values.filter(
			(item) => item.meta.product_attribute_id === id
		);
	};
</script>

<template>
	<div class="card">
		<div class="card-header">
			<p class="fw-semibold m-0">{{ $t("products.variants") }}</p>
		</div>
		<div class="card-body">
			<div class="row g-3">
				<div class="col-12">
					<BaseMultiSelect
						v-model="attributeSelection"
						:form-label="$t('products.attributes')"
						:options="lookup.product_attributes"
						:placeholder="$t('select.one')"
						:close-on-select="true"
						mode="tags"
					/>
				</div>
				<div v-if="selectedAttributes.length > 0" class="col-12">
					<div
						class="d-flex flex-wrap gap-3 align-items-stretch justify-content-start"
					>
						<div
							v-for="data in selectedAttributes"
							:key="data.attribute.value"
							class="border flex-grow-1 rounded py-2 px-3"
						>
							<p class="fw-semibold mb-2">{{ data.attribute.label }}</p>
							<BaseCheckbox
								v-for="attr in getAttributeValues(data.attribute.value)"
								:key="attr.value"
								v-model="data.valueIds"
								:value="attr.value"
								:label="attr.title"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
