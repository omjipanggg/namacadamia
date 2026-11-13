<script setup>
	import { computed, reactive, ref, watch } from "vue";

	import { generateVariants, getSkuKey } from "@/resources/products.js";
	import BaseMoneyInput from "@/components/base/BaseMoneyInput.vue";

	const skus = defineModel({
		type: Array,
		default: () => [],
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
	});

	const groups = computed(() =>
		props.selectedAttributes.map((group) => ({
			attribute: group.attribute,
			values: group.values.map((value) => ({
				id: value.id ?? null,
				label: value.label,
				attribute_id: group.attribute.id,
				abbr: value.abbr ?? null,
			})),
		}))
	);

	const hasEmptyGroup = computed(() =>
		groups.value.some((group) => group.values.length === 0)
	);

	const activeSkus = computed(() =>
		skus.value.filter((sku) => sku.is_active !== false)
	);

	const showBulkPrice = ref(false);

	const bulkPrice = reactive({
		costPrice: 0,
		sellingPrice: 0,
	});

	const openBulkPrice = () => {
		const firstSku = activeSkus.value[0];

		if (firstSku) {
			bulkPrice.costPrice = firstSku.cost_price;
			bulkPrice.sellingPrice = firstSku.selling_price;
		}

		showBulkPrice.value = !showBulkPrice.value;
	};

	const applyPrices = () => {
		activeSkus.value.forEach((sku) => {
			sku.cost_price = bulkPrice.costPrice;
			sku.selling_price = bulkPrice.sellingPrice;
		});

		showBulkPrice.value = false;
	};

	watch(
		groups,
		(newGroups) => {
			const existingSkus = skus.value;

			const existingByKey = new Map(
				existingSkus.map((sku) => [sku.key || getSkuKey(sku.values ?? []), sku])
			);

			const generated =
				newGroups.length && !hasEmptyGroup.value
					? generateVariants(newGroups, props.code)
					: [];

			const generatedKeys = new Set(generated.map((sku) => sku.key));

			const nextSkus = generated.map((variant) => {
				const existing = existingByKey.get(variant.key);

				return existing
					? {
							...existing,
							values: variant.values,
							is_active: true,
						}
					: variant;
			});

			const removedPersistedSkus = existingSkus
				.filter((sku) => sku.id != null && !generatedKeys.has(sku.key))
				.map((sku) => ({
					...sku,
					is_active: false,
				}));

			skus.value = [...nextSkus, ...removedPersistedSkus];
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
					v-if="activeSkus.length"
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

			<div v-if="!activeSkus.length">
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
							<tr v-for="sku in activeSkus" :key="sku.key">
								<td width="240">
									<input
										:id="`variants-${sku.key}`"
										v-tooltip
										type="text"
										disabled
										class="form-control disabled text-truncate"
										:value="sku.values.map((value) => value.label).join(' · ')"
										:title="sku.values.map((value) => value.label).join(' · ')"
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
