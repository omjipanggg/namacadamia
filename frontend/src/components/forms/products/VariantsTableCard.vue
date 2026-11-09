<script setup>
	import { computed, reactive, ref, watch } from "vue";
	import { generateVariants } from "@/resources/products.js";
	import BaseMoneyInput from "@/components/base/BaseMoneyInput.vue";

	const skus = defineModel({
		type: Array,
		default: [],
	});

	const props = defineProps({
		code: {
			type: String,
			default: null,
		},
		selectedAttributes: {
			type: Array,
			default: () => [],
		},
		lookup: {
			type: Object,
			default: () => ({}),
		},
	});

	const groups = computed(() => {
		return props.selectedAttributes.map((attribute) => ({
			attribute: attribute.attribute,
			values: props.lookup.product_attribute_values.filter(
				(child) =>
					child.meta?.product_attribute_id === attribute.attribute.value &&
					attribute.valueIds.includes(child.value)
			),
		}));
	});

	const hasEmptyGroup = computed(() => {
		return groups.value.some((group) => group.values.length === 0);
	});

	const showBulkPrice = ref(false);

	const bulkPrice = reactive({
		costPrice: 0,
		sellingPrice: 0,
	});

	const openBulkPrice = () => {
		const firstSku = skus.value[0];

		if (firstSku) {
			bulkPrice.costPrice = firstSku.cost_price;
			bulkPrice.sellingPrice = firstSku.selling_price;
		}

		showBulkPrice.value = !showBulkPrice.value;
	};

	const applyPrices = () => {
		skus.value.forEach((sku) => {
			sku.cost_price = bulkPrice.costPrice;
			sku.selling_price = bulkPrice.sellingPrice;
		});
		showBulkPrice.value = false;
	};

	watch(
		groups,
		(newGroups) => {
			if (newGroups.length === 0 || hasEmptyGroup.value) {
				skus.value = [];
				return;
			}

			const generated = generateVariants(newGroups, props.code);

			const existing = new Map(skus.value.map((sku) => [sku.key, sku]));

			skus.value = generated.map((variant) => {
				return existing.get(variant.key) ?? variant;
			});
		},
		{
			deep: true,
			immediate: true,
		}
	);
</script>

<template>
	<div class="card">
		<div class="card-header">
			<div
				class="d-flex flex-wrap gap-3 align-items-center justify-content-between"
			>
				<p class="m-0 fw-semibold">SKU {{ $t("products.title") }}</p>

				<button
					v-if="groups.some((item) => item.values?.length != 0)"
					type="button"
					class="btn btn-dark border btn-sm"
					@click="openBulkPrice"
				>
					{{ showBulkPrice ? $t("common.hide") : $t("products.setAllPrices") }}
					<i
						:class="['bi', `bi-caret-${showBulkPrice ? 'up' : 'down'}-fill`]"
					></i>
				</button>
			</div>
		</div>
		<div class="card-body">
			<div v-if="showBulkPrice" id="bulk-price-editor">
				<div class="border rounded p-3 mb-3">
					<div class="row g-3">
						<div class="col-md-6">
							<BaseMoneyInput
								id="bulkCostPrice"
								v-model="bulkPrice.costPrice"
								:label="$t('products.cost')"
							/>
						</div>

						<div class="col-md-6">
							<BaseMoneyInput
								id="bulkSellingPrice"
								v-model="bulkPrice.sellingPrice"
								:label="$t('products.selling')"
							/>
						</div>

						<div class="col-12 text-end">
							<button
								type="button"
								class="btn btn-primary"
								@click="applyPrices"
							>
								{{ $t("common.apply") }}
								<i class="bi bi-save ms-2"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div
				v-if="
					groups.some((item) => item.values?.length === 0) ||
					groups.length === 0
				"
			>
				{{ $t("products.noVariantsAdded") }}
			</div>
			<div v-else class="border p-1 rounded">
				<div class="table-responsive border rounded">
					<table
						class="table m-0 table-hover table-striped text-nowrap align-middle"
					>
						<thead>
							<tr>
								<th>{{ $t("products.variants") }}</th>
								<th>{{ $t("products.skuCode") }}</th>
								<th>{{ $t("products.barcode") }}</th>
								<th>{{ $t("products.cost") }}</th>
								<th>{{ $t("products.selling") }}</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="sku in skus" :key="sku.key">
								<td width="240">
									<input
										:id="`variants-${sku.key}`"
										v-tooltip
										type="text"
										disabled
										class="form-control disabled text-truncate"
										:value="
											(sku.values ?? []).map((value) => value.title).join(' · ')
										"
										:title="
											(sku.values ?? []).map((value) => value.title).join(' · ')
										"
									/>
								</td>
								<td width="270">
									<input
										:id="`code-${sku.key}`"
										v-model="sku.code"
										v-tooltip
										:title="sku.code"
										type="text"
										class="form-control text-truncate"
									/>
								</td>
								<td width="270">
									<input
										:id="`barcode-${sku.key}`"
										v-model="sku.barcode"
										type="text"
										class="form-control"
									/>
								</td>
								<td width="200">
									<input
										:id="`cost-${sku.key}`"
										v-model.number="sku.cost_price"
										type="number"
										class="form-control"
									/>
								</td>
								<td width="200">
									<input
										:id="`selling-${sku.key}`"
										v-model.number="sku.selling_price"
										type="number"
										class="form-control"
									/>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
	th {
		padding-left: 12px;
	}
	td {
		font-family: "Courier New", Courier, monospace;
	}
</style>
