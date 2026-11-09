<script setup>
	import { formatMoney } from "@/helpers/format.js";
	import BaseTable from "@/components/table/TableMaster.vue";
	defineProps({
		raw: {
			type: [Array, Object],
			default: null,
		},
		columns: {
			type: Array,
			default: () => [],
		},
	});
</script>

<template>
	<BaseTable
		:raw="raw"
		:columns="columns"
		:searchable="false"
		:info="false"
		:paging="false"
		:has-pagination="false"
		hover
	>
		<template #cell-product_name="{ row }">
			<p class="m-0">{{ row.product_name }}</p>
			<small class="small text-muted">{{ row.variant_label }}</small>
		</template>

		<template #cell-revenue="{ row }">{{ formatMoney(row.revenue) }}</template>
	</BaseTable>
</template>
