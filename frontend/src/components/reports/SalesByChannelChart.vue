<script setup>
	import { computed } from "vue";
	import { formatMoney } from "@/helpers/format.js";
	import BaseCard from "@/components/base/BaseCard.vue";

	import {
		Chart as ChartJS,
		Title,
		Tooltip,
		Legend,
		ArcElement,
	} from "chart.js";

	import { Doughnut } from "vue-chartjs";

	ChartJS.register(Title, Tooltip, Legend, ArcElement);

	const props = defineProps({
		data: {
			type: Array,
			default: () => [],
		},
	});

	const bootstrapColors = [
		"#fd7e14", // Orange
		"#0dcaf0", // Info
		"#198754", // Success
		"#0d6efd", // Primary
		"#ffc107", // Warning
		"#dc3545", // Danger
		"#6f42c1", // Purple
		"#d63384", // Pink
		"#20c997", // Teal
		"#6610f2", // Indigo
	];

	const chartData = computed(() => ({
		labels: props.data.map((item) => item.channel_name),
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

		plugins: {
			legend: {
				position: "bottom",
			},

			tooltip: {
				callbacks: {
					label: (context) => {
						const value = Number(context.raw) || 0;
						const total = context.dataset.data.reduce(
							(sum, value) => sum + Number(value || 0),
							0
						);
						const percentage =
							total > 0 ? ((value / total) * 100).toFixed(1) : 0;
						const formattedValue = formatMoney(value);
						return `${formattedValue} (${percentage}%)`;
					},
				},
			},
		},
	};
</script>

<template>
	<BaseCard>
		<template #header>{{ $t("dashboard.reports.salesByChannel") }}</template>
		<div class="chart-container">
			<Doughnut :data="chartData" :options="chartOptions" />
		</div>
	</BaseCard>
</template>

<style scoped>
	.chart-container {
		min-height: 360px;
	}
</style>
