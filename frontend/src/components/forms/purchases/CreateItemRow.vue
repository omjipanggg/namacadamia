<script setup>
	import { computed } from "vue";
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
	});

	const taggable = {
		label: "SKU",
		action: () => router.push({ name: "dashboard.products.index" }),
	};

	const emit = defineEmits(["remove"]);

	const subtotal = computed(() => {
		const quantity = item.value.quantity || 0;
		const costPrice = item.value.cost_price || 0;

		const selectedUnit = props.lookup.units.find(
			(unit) => unit.value === item.value.measure_unit_id
		);
		const ratio = Number(selectedUnit?.subtitle) || 1;

		return quantity * ratio * costPrice;
	});
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
					props.lookup.skus.filter(
						(sku) => !props.excludedIds.includes(sku.value)
					)
				"
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
					v-for="unit in props.lookup.units"
					:key="unit.value"
					:value="unit.value"
				>
					{{ unit.title }} ({{ unit.subtitle }})
				</option>
			</select>
		</td>
		<td>
			<BaseMoneyInput
				:id="`cost-price-${item.id}`"
				v-model="item.cost_price"
				:placeholder="$t('tables.costPrice')"
				min="0"
				required
			/>
		</td>
		<td>
			<input
				:id="`subtotal-${item.id}`"
				:value="subtotal.toLocaleString()"
				type="text"
				required
				readonly
				class="form-control"
				disabled
			/>
		</td>
	</tr>
</template>
