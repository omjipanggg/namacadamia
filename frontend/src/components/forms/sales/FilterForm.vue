<script setup>
	import { saleStatus } from "@/resources/sales.js";
	import { useSaleStore } from "@/stores/sales.js";

	import BaseDatePicker from "@/components/base/BaseDatePicker.vue";
	import BaseSelect from "@/components/base/BaseSelect.vue";

	const saleStore = useSaleStore();
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
				:disabled="saleStore.loading"
			/>
		</div>
		<div class="col-lg-4 col-md-12">
			<BaseSelect
				v-model="filters.payment_method_id"
				:label="$t('common.methods')"
				:options="lookup.payment_methods"
				:placeholder="
					$t('select.option', { item: `[${$t('common.methods')}]` })
				"
				:disabled="saleStore.loading"
			/>
		</div>

		<div class="col-lg-4 col-md-12">
			<BaseSelect
				v-model="filters.status"
				label="Status"
				:options="
					saleStatus.map((item, index) => ({
						title: item.charAt(0).toUpperCase() + item.slice(1),
						value: item,
					}))
				"
				:disabled="saleStore.loading"
				:placeholder="$t('select.option', { item: `[Status]` })"
			/>
		</div>
	</div>
</template>
