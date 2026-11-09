<script setup>
	import BaseInput from "@/components/base/BaseInput.vue";
	import BaseMultiSelect from "@/components/base/BaseMultiSelect.vue";
	import BaseCheckbox from "@/components/base/BaseCheckbox.vue";
	import BaseTextarea from "@/components/base/BaseTextarea.vue";
	import BaseMoneyInput from "@/components/base/BaseMoneyInput.vue";

	const name = defineModel("name", {
		type: String,
	});
	const price = defineModel("price", {
		type: [String, Number],
	});
	const description = defineModel("description", {
		type: String,
	});
	const categories = defineModel("categories", {
		type: Array,
	});
	const hasVariants = defineModel("hasVariants", {
		type: Boolean,
	});

	defineProps({
		errors: {
			type: Object,
			default: () => ({}),
		},
		lookup: {
			type: Object,
			default: () => ({
				product_categories: [],
				product_attribute_values: [],
				product_attributes: [],
			}),
		},
	});
</script>

<template>
	<div class="card">
		<div class="card-header">
			<p class="fw-semibold m-0">{{ $t("products.detail") }}</p>
		</div>
		<div class="card-body">
			<div class="row g-3">
				<div class="col-12">
					<div class="d-flex flex-column gap-3">
						<BaseCheckbox
							v-model="hasVariants"
							:label="$t('products.hasVariants')"
						/>

						<div class="row g-3">
							<div class="col-xl-6">
								<BaseInput
									v-model="name"
									:label="$t('common.name')"
									:placeholder="$t('products.inputName')"
									:error="errors.name"
								/>
							</div>
							<div class="col-xl-6">
								<BaseMoneyInput
									v-model="price"
									:label="$t('products.displayPrice')"
									:placeholder="$t('products.enterAmount')"
									:error="errors.price"
								/>
							</div>
						</div>
						<BaseMultiSelect
							v-model="categories"
							:options="lookup.product_categories"
							:form-label="$t('products.categories')"
							:placeholder="$t('select.more')"
							:error="errors.categories"
							mode="tags"
							:close-on-select="false"
						/>
						<BaseTextarea
							v-model="description"
							:label="$t('columns.description')"
							:placeholder="$t('common.optional')"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
