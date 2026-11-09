<script setup>
	import {
		Chart as ChartJS,
		Title,
		Tooltip,
		Legend,
		CategoryScale,
		LinearScale,
		PointElement,
		LineElement,
	} from "chart.js";
	import {
		formatDateISO,
		formatMoney,
		formatMKNumber,
	} from "@/helpers/format.js";
	import { Line } from "vue-chartjs";
	import { computed } from "vue";

	import BaseCard from "@/components/base/BaseCard.vue";

	ChartJS.register(
		Title,
		Tooltip,
		Legend,
		CategoryScale,
		LinearScale,
		PointElement,
		LineElement
	);

	const props = defineProps({
		data: {
			type: Array,
			default: () => [],
		},
	});

	const chartData = computed(() => ({
		labels: props.data.map((item) => formatDateISO(item.period)),
		datasets: [
			{
				label: "Net Sales",
				data: props.data.map((item) => item.net_sales),
				backgroundColor: "#0d6efd",
				borderColor: "#0d6efd",
				borderWidth: 3.2,
				tension: 0.36,
			},
		],
	}));

	const chartOptions = {
		responsive: true,
		maintainAspectRatio: false,
		plugins: {
			legend: {
				display: false,
			},
			tooltip: {
				callbacks: {
					label: (context) => {
						return formatMoney(context.raw);
					},
				},
			},
		},
		scales: {
			y: {
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
		<template #header>{{ $t("sales.trends") }}</template>
		<div class="chart-container">
			<Line :data="chartData" :options="chartOptions" />
		</div>
	</BaseCard>
</template>

<style scoped>
	.chart-container {
		min-height: 360px;
	}
</style>
