<script setup>
	import { onMounted, ref, computed } from "vue";
	import { storeToRefs } from "pinia";
	import api from "@/services/api.js";
	import cardsObj from "@/resources/cards.js";
	import DashboardCard from "./DashboardCard.vue";
	import BaseCard from "@/components/base/BaseCard.vue";
	import SalesAndPurchasesTrendsChart from "@/components/reports/SalesAndPurchasesTrendsChart.vue";
	import { useReportStore } from "@/stores/reports.js";

	const reportStore = useReportStore();
	const { salesAndPurchasesTrends, loading } = storeToRefs(reportStore);

	const stats = ref({});

	const cards = computed(() => {
		return cardsObj.map((card) => ({
			...card,
			value: stats.value?.resource?.[card.key] ?? 0,
		}));
	});

	const fetchStats = async (params = {}) => {
		const response = await api.get("/api/dashboard", params);
		stats.value = response;
	};

	onMounted(() => {
		fetchStats();
		reportStore.fetchSalesReport();
	});
</script>

<template>
	<div v-for="card in cards" :key="card.title" class="col-xl-3 col-md-6">
		<DashboardCard :card="card" :ribbon="true" />
	</div>
	<div
		v-if="stats.resource?.stocks && stats.resource?.stocks.length > 0"
		class="col-xl-4"
	>
		<BaseCard>
			<template #header>
				{{ $t("stocks.available") }}
			</template>
			<div class="table-responsive">
				<table class="table table-hover table-bordered m-0">
					<thead>
						<tr>
							<th>{{ $t("branches.title") }}</th>
							<th class="text-end">{{ $t("tables.total") }}</th>
						</tr>
					</thead>
					<tbody>
						<template v-if="loading">
							<tr v-for="n in 3" :key="n" class="placeholder-glow">
								<td><span class="placeholder col-12"></span></td>
								<td><span class="placeholder col-12"></span></td>
							</tr>
						</template>
						<template v-else>
							<tr v-for="stock in stats.resource?.stocks" :key="stock.key">
								<td>{{ stock.title }}</td>
								<td class="text-end">{{ stock.value }}</td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>
		</BaseCard>
	</div>
	<div
		:class="
			stats.resource?.stocks && stats.resource?.stocks.length > 0
				? 'col-xl-8'
				: 'col-xl-12'
		"
	>
		<SalesAndPurchasesTrendsChart :data="salesAndPurchasesTrends" />
	</div>
</template>
