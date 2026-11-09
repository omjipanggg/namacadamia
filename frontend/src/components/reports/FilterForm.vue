<script setup>
	import { useReportStore } from "@/stores/reports.js";
	import BaseDatePicker from "@/components/base/BaseDatePicker.vue";
	import BaseSelect from "@/components/base/BaseSelect.vue";

	const reportStore = useReportStore();

	const periodOptions = [
		{
			title: "Day",
			value: "day",
		},
		{
			title: "Week",
			value: "week",
		},
		{
			title: "Month",
			value: "month",
		},
		{
			title: "Year",
			value: "year",
		},
	];

	const filters = defineModel({
		type: Object,
		required: true,
		default: () => ({}),
	});

	defineProps({
		lookup: {
			type: Object,
			default: () => ({}),
		},
	});
</script>

<template>
	<div class="row g-3">
		<div class="col-sm-6">
			<BaseDatePicker v-model="filters.date_from" :label="$t('date.from')" />
		</div>
		<div class="col-sm-6">
			<BaseDatePicker v-model="filters.date_to" :label="$t('date.to')" />
		</div>
		<div class="col-lg-4 col-md-12">
			<BaseSelect
				v-model="filters.branch_id"
				:label="$t('branches.title')"
				:options="lookup.branches"
				:placeholder="
					$t('select.option', { item: `[${$t('branches.title')}]` })
				"
				:disabled="reportStore.loading"
			/>
		</div>
		<div class="col-lg-4 col-md-12">
			<BaseSelect
				v-model="filters.sales_channel_id"
				:label="$t('sales.channel')"
				:options="lookup.sales_channels"
				:placeholder="$t('select.option', { item: `[${$t('sales.channel')}]` })"
				:disabled="reportStore.loading"
			/>
		</div>
		<div class="col-lg-4 col-md-12">
			<BaseSelect
				v-model="filters.group_by"
				:label="$t('dashboard.reports.period')"
				:options="periodOptions"
				:placeholder="
					$t('select.option', { item: `[${$t('dashboard.reports.period')}]` })
				"
				:disabled="reportStore.loading"
			/>
		</div>
	</div>
</template>
