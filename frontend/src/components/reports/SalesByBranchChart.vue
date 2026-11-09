<script setup>
	import { computed } from "vue";
	import { formatMoney, formatMKNumber } from "@/helpers/format.js";
	import BaseCard from "@/components/base/BaseCard.vue";

	import {
		Chart as ChartJS,
		Title,
		Tooltip,
		Legend,
		CategoryScale,
		LinearScale,
		BarElement,
	} from "chart.js";

	import { Bar } from "vue-chartjs";

	ChartJS.register(
		Title,
		Tooltip,
		Legend,
		CategoryScale,
		LinearScale,
		BarElement
	);

	const props = defineProps({
		data: {
			type: Array,
			default: () => [],
		},
	});

	const bootstrapColors = [
		"#0d6efd", // Primary
		"#ffc107", // Warning
		"#dc3545", // Danger
		"#198754", // Success
		"#0dcaf0", // Info
		"#6f42c1", // Purple
		"#d63384", // Pink
		"#fd7e14", // Orange
		"#20c997", // Teal
		"#6610f2", // Indigo
	];

	const chartData = computed(() => ({
		labels: props.data.map((item) => item.branch_code),
		datasets: [
			{
				label: "Net Sales",
				data: props.data.map((item) => item.net_sales),
				backgroundColor: props.data.map(
					(_, index) => bootstrapColors[index % bootstrapColors.length]
				),
				borderColor: props.data.map(
					(_, index) => bootstrapColors[index % bootstrapColors.length]
				),
				borderWidth: 1,
			},
		],
	}));

	const chartOptions = {
		responsive: true,
		maintainAspectRatio: false,
		indexAxis: "y",
		plugins: {
			legend: {
				display: false,
			},
			tooltip: {
				callbacks: {
					title: (context) => {
						const index = context[0].dataIndex;
						return props.data[index]?.branch_name || context[0].label;
					},
					label: (context) => {
						return formatMoney(context.raw);
					},
				},
			},
		},
		scales: {
			x: {
				beginAtZero: true,

				ticks: {
					callback: (value) => {
						return formatMKNumber(value);
					},
				},
			},
		},
	};
</script>

<template>
	<BaseCard>
		<template #header>{{ $t("dashboard.reports.salesByBranch") }}</template>
		<div class="chart-container">
			<Bar :data="chartData" :options="chartOptions" />
		</div>
	</BaseCard>
</template>

<style scoped>
	.chart-container {
		min-height: 360px;
	}
</style>
