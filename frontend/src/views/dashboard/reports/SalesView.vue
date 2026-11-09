<script setup>
	import { onMounted, reactive, ref } from "vue";
	import { storeToRefs } from "pinia";
	import { formatToInputString } from "@/helpers/format.js";
	import { useLookupStore } from "@/stores/lookup.js";
	import { useReportStore } from "@/stores/reports.js";

	import Preloader from "@/components/Preloader.vue";
	import FormModal from "@/components/modal/FormModal.vue";
	import FilterForm from "@/components/reports/FilterForm.vue";
	import BaseButton from "@/components/base/BaseButton.vue";

	import SaleSummaryCard from "@/components/reports/SaleSummaryCard.vue";
	import SalesTrendChart from "@/components/reports/SalesTrendChart.vue";
	import SalesByBranchChart from "@/components/reports/SalesByBranchChart.vue";
	import SalesByChannelChart from "@/components/reports/SalesByChannelChart.vue";
	import TopProductsTable from "@/components/reports/TopProductsTable.vue";

	const lookup = useLookupStore();
	const reportStore = useReportStore();

	const {
		salesReport,
		summary,
		salesTrend,
		salesByBranch,
		salesByChannel,
		topProducts,
		topProductColumns,
		loading,
		error,
	} = storeToRefs(reportStore);

	const now = new Date();
	const initForm = () => ({
		date_from: formatToInputString(
			new Date(now.getFullYear(), now.getMonth(), 1).toLocaleString()
		),
		date_to: formatToInputString(
			new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59)
		),
		branch_id: null,
		sales_channel_id: null,
		group_by: "day",
	});

	const filters = reactive(initForm());

	const fetchSalesReport = async (params = {}) => {
		applyingFilters.value = true;
		try {
			await reportStore.fetchSalesReport({
				...filters,
				...params,
			});
		} finally {
			showFilters.value = false;
			applyingFilters.value = false;
		}
	};

	onMounted(() => {
		fetchSalesReport();
		lookup.load(["branches", "sales_channels"], true);
	});

	const showFilters = ref(false);
	const applyingFilters = ref(false);

	const resetFilters = () => {
		Object.assign(filters, initForm());
	};
</script>

<template>
	<FormModal
		v-model="showFilters"
		:title="$t('common.filter')"
		:loading="reportStore.loading"
		size="lg"
		:overflow-visible="true"
	>
		<FilterForm v-model="filters" :lookup="lookup" />

		<template #footer>
			<BaseButton
				variant="danger"
				:disabled="reportStore.loading"
				@click="resetFilters"
			>
				{{ $t("common.reset") }}
				<i class="bi bi-arrow-repeat ms-2"></i>
			</BaseButton>
			<div class="d-flex gap-3 flex-wrap">
				<BaseButton
					type="button"
					variant="secondary"
					:disabled="reportStore.loading"
					@click="showFilters = false"
				>
					{{ $t("common.cancel") }}
					<i class="bi bi-x-square ms-2"></i>
				</BaseButton>
				<BaseButton
					variant="success"
					:loading="applyingFilters"
					:disabled="reportStore.loading"
					@click="fetchSalesReport"
				>
					{{ $t("common.apply") }}
					<i class="bi bi-funnel"></i>
				</BaseButton>
			</div>
		</template>
	</FormModal>
	<div class="col-12">
		<div
			class="d-flex mb-3 flex-wrap align-items-center justify-content-between"
		>
			<h3>{{ $t("dashboard.reports.sales") }}</h3>
			<BaseButton
				variant="primary"
				:disabled="loading"
				@click="showFilters = true"
			>
				{{ $t("common.filter") }}
				<i class="bi bi-filter-left"></i>
			</BaseButton>
		</div>
		<div v-if="error">
			<div class="alert alert-danger">
				{{
					$t("errors.fetchFailed", { entity: $t("sales.title").toLowerCase() })
				}}
			</div>
		</div>
		<div v-else-if="loading">
			<div class="text-center">
				<div class="d-flex align-items-center flex-wrap gap-2">
					<Preloader variant="border" />
					<span>{{ $t("common.fetching") }}</span>
				</div>
			</div>
		</div>
		<div v-else-if="salesReport">
			<div class="row g-3">
				<SaleSummaryCard :summary="summary" />

				<div class="col-12">
					<SalesTrendChart :data="salesTrend" />
				</div>

				<div class="col-lg-7">
					<SalesByBranchChart :data="salesByBranch" />
				</div>

				<div class="col-lg-5">
					<SalesByChannelChart :data="salesByChannel" />
				</div>

				<div class="col-12">
					<TopProductsTable :raw="topProducts" :columns="topProductColumns" />
				</div>
			</div>
		</div>
	</div>
</template>
