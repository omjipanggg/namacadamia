<script setup>
	import { computed, watch, watchEffect } from "vue";
	import { useRouter } from "vue-router";
	import LookupSelect from "@/components/base/BaseLookupSelect.vue";
	import BaseMoneyInput from "@/components/base/BaseMoneyInput.vue";

	const router = useRouter();

	const item = defineModel("item", {
		type: Object,
		required: true,
	});

	const props = defineProps({
		excludedIds: {
			type: Array,
			default: () => [],
		},
		lookup: {
			type: Object,
			default: () => ({}),
		},
		selectedBranchId: {
			type: [Number, String],
			default: null,
		},
	});

	const taggable = {
		label: "SKU",
		action: () => router.push({ name: "dashboard.products.index" }),
	};

	const emit = defineEmits(["remove"]);

	const filteredSkus = computed(() => {
		if (!props.selectedBranchId) return [];

		const branchId = Number(props.selectedBranchId);

		const branchStockLocationIds = (props.lookup?.stock_locations || [])
			.filter((location) => Number(location.meta?.branch_id) === branchId)
			.map((location) => location.value);

		return (props.lookup?.skus || []).filter((sku) => {
			if (props.excludedIds.includes(sku.value)) return false;

			const skuLocationIds = sku.meta?.stock_locations || [];

			return skuLocationIds.some((locId) =>
				branchStockLocationIds.includes(locId)
			);
		});
	});

	watchEffect(() => {
		const quantity = item.value.quantity || 0;
		const price = item.value.price || 0;
		const discount = item.value.discount || 0;

		const selectedUnit = props.lookup.units.find(
			(unit) => unit.value === item.value.measure_unit_id
		);
		const ratio = Number(selectedUnit?.subtitle) || 1;

		const someValue = quantity * ratio * price;

		item.value.subtotal = someValue - Math.min(discount, someValue);
	});

	watch(
		() => item.value?.product_sku_id,
		(newValue) => {
			if (!newValue) {
				item.value.price = 0;
				return;
			}
			const selectedSku = props.lookup.skus.find(
				(sku) => sku.value === newValue
			);

			if (selectedSku) {
				item.value.price = Number(selectedSku.meta.price);
			}
		}
	);
</script>

<template>
	<tr>
		<td class="text-center">
			<button type="button" class="btn btn-danger" @click="emit('remove')">
				<i class="bi bi-trash"></i>
			</button>
		</td>
		<td>
			<LookupSelect
				v-model="item.product_sku_id"
				:items="
					// lookup.skus.filter((sku) => !excludedIds.includes(sku.value) && true)
					filteredSkus
				"
				:disabled="!selectedBranchId"
				:placeholder="$t('select.option', { item: `[SKU]` })"
				:taggable="taggable"
				@action="(actionItem) => actionItem.action()"
			/>
		</td>
		<td>
			<input
				:id="`qty-${item.id}`"
				v-model.number="item.quantity"
				type="number"
				class="form-control"
				min="1"
				placeholder="Qty"
				required
			/>
		</td>
		<td>
			<select
				:id="`measure-unit-${item.id}`"
				v-model="item.measure_unit_id"
				class="form-select"
				required
			>
				<option :value="null" disabled>
					{{ $t("select.one") }}
				</option>
				<option
					v-for="unit in lookup.units"
					:key="unit.value"
					:value="unit.value"
				>
					{{ unit.title }} ({{ unit.subtitle }})
				</option>
			</select>
		</td>
		<td>
			<BaseMoneyInput
				:id="`price-${item.id}`"
				v-model="item.price"
				:placeholder="$t('tables.price')"
				required
			/>
		</td>
		<td>
			<BaseMoneyInput
				:id="`discount-${item.id}`"
				v-model="item.discount"
				:placeholder="$t('tables.discount')"
				:max="item.price"
			/>
		</td>
		<td>
			<BaseMoneyInput
				:id="`subtotal-${item.id}`"
				v-model="item.subtotal"
				readonly
				disabled
			/>
		</td>
	</tr>
</template>
