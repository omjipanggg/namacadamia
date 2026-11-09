<script setup>
	import { onMounted } from "vue";
	import { formatMoney } from "@/helpers/format.js";
	import { useSaleStore } from "@/stores/sales.js";
	import BaseButton from "@/components/base/BaseButton.vue";
	import Table from "@/components/table/TableMaster.vue";
	import SaleReceipt from "@/components/prints/SaleReceipt.vue";

	const saleStore = useSaleStore();

	const columns = [
		{
			key: "code",
			label: "Code",
			sortable: true,
		},
		{
			key: "branch.name",
			label: "Branch",
		},
		{
			key: "channel.name",
			label: "Channel",
		},
		{
			key: "subtotal",
			label: "Total",
			formatter: (row) => formatMoney(row.subtotal),
		},
		{
			key: "created_at",
			label: "Order Date",
			sortable: true,
		},
		{
			key: "actions",
			label: "Actions",
		},
	];

	const fetchSales = async (params) => {
		await saleStore.fetch(params);
	};

	onMounted(() => {
		fetchSales();
	});

	const handlePrint = async (code) => {
		await saleStore.show(code);
		window.print();
	};
</script>

<template>
	<SaleReceipt v-if="saleStore?.saleDetail" :sale="saleStore?.saleDetail" />
	<div class="col-12">
		<h2 class="mb-0">{{ $t("dashboard.reports.invoices") }}</h2>
		<Table
			striped
			hover
			:columns="columns"
			:raw="saleStore.sales"
			@request="fetchSales"
		>
			<template #cell-actions="{ row }">
				<BaseButton
					variant="outline-primary"
					size="sm"
					:loading="saleStore?.loading"
					@click.stop="handlePrint(row.code)"
				>
					{{ $t("common.preview") }}
					<i class="bi bi-box-arrow-up-right ms-1"></i>
				</BaseButton>
			</template>
		</Table>
	</div>
</template>

<style scoped></style>
