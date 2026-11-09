<script setup>
	import { onMounted, reactive, ref } from "vue";
	import { useRouter } from "vue-router";
	import { columns } from "@/resources/sales.js";
	import { formatToInputString, sentenceCase } from "@/helpers/format.js";
	import BaseButton from "@/components/base/BaseButton.vue";
	import BaseBadge from "@/components/base/BaseBadge.vue";
	import Table from "@/components/table/TableMaster.vue";
	import FormModal from "@/components/modal/FormModal.vue";
	import FilterForm from "@/components/forms/sales/FilterForm.vue";
	import { useLookupStore } from "@/stores/lookup.js";
	import { useAuthStore } from "@/stores/auth.js";
	import { useSaleStore } from "@/stores/sales.js";

	const auth = useAuthStore();
	const lookup = useLookupStore();
	const saleStore = useSaleStore();

	const now = new Date();

	const initialForm = () => ({
		search: "",
		status: "all",
		branch_id: "",
		sales_channel_id: "",
		payment_method_id: "",
		date_from: formatToInputString(
			new Date(now.getFullYear(), 0, 1).toLocaleString()
		),
		date_to: formatToInputString(
			new Date(now.getFullYear(), 12, 0, 23, 59, 59)
		),
	});

	const filters = reactive(initialForm());

	const resetFilters = () => {
		Object.assign(filters, initialForm());
	};

	const showFilters = ref(false);
	const selectedSales = ref([]);
	const applyingFilters = ref(false);

	const fetchSales = async (params = {}) => {
		applyingFilters.value = true;
		try {
			await saleStore.fetch({ ...filters, ...params });
		} catch (error) {
			console.error(error);
		} finally {
			showFilters.value = false;
			applyingFilters.value = false;
		}
	};

	const router = useRouter();
	const redirectToCreate = () => {
		router.push({
			name: "sales.create",
		});
	};

	onMounted(() => {
		fetchSales();
		lookup.load(["branches", "sales_channels", "payment_methods"], true);
	});

	const getStatusConfig = (status) => {
		const statusConfig = {
			completed: { variant: "success", icon: "bi-check-circle" },
			pending: { variant: "warning", icon: "bi-clock" },
			partial: { variant: "info", icon: "bi-info-circle" },
			paid: { variant: "success", icon: "bi-check-circle" },
			cancelled: { variant: "danger", icon: "bi-x-circle" },
		};
		return (
			statusConfig[status] || {
				variant: "secondary",
				icon: "bi-question-circle",
			}
		);
	};
</script>

<template>
	<Teleport defer to="#btnAddOnNavbar">
		<BaseButton
			v-if="auth.isAdmin"
			type="button"
			variant="outline-secondary"
			@click="redirectToCreate"
		>
			<i class="bi bi-plus-square me-2"></i>
			{{ $t("common.addNew", { item: $t("sales.title") }) }}
		</BaseButton>
	</Teleport>
	<FormModal
		v-model="showFilters"
		:title="$t('common.filter')"
		:loading="saleStore.loading"
		:overflow-visible="true"
		size="xl"
	>
		<FilterForm v-model="filters" :lookup="lookup" />
		<template #footer>
			<BaseButton
				variant="danger"
				:disabled="saleStore.loading"
				@click="resetFilters"
			>
				{{ $t("common.reset") }}
				<i class="bi bi-arrow-repeat ms-2"></i>
			</BaseButton>
			<div class="d-flex gap-3 flex-wrap">
				<BaseButton
					type="button"
					variant="secondary"
					:disabled="saleStore.loading"
					@click="showFilters = false"
				>
					{{ $t("common.cancel") }}
					<i class="bi bi-x-square ms-2"></i>
				</BaseButton>
				<BaseButton
					variant="success"
					:loading="applyingFilters"
					:disabled="saleStore.loading"
					@click="fetchSales"
				>
					{{ $t("common.apply") }}
					<i class="bi bi-funnel"></i>
				</BaseButton>
			</div>
		</template>
	</FormModal>
	<div class="col-12">
		<h2 class="mb-0">{{ $t("sales.title") }}</h2>
		<Table
			v-model:selected="selectedSales"
			striped
			hover
			selectable
			:columns="columns"
			:raw="saleStore.sales"
			@request="fetchSales"
		>
			<template #toolbar-end>
				<BaseButton
					variant="primary"
					type="button"
					:disabled="saleStore.loading"
					@click="showFilters = !showFilters"
				>
					{{ $t("common.filter") }}
					<i class="bi bi-filter-left"></i>
				</BaseButton>
			</template>
			<template #cell-code="{ row }">
				<RouterLink
					:to="{
						name: 'sales.show',
						params: { code: row.code },
					}"
					class="text-decoration-none fw-semibold"
				>
					{{ row.code }}
				</RouterLink>
			</template>
			<template #cell-status="{ row }">
				<BaseBadge :variant="getStatusConfig(row.status).variant">
					<i :class="getStatusConfig(row.status).icon" class="me-2"></i>
					{{ sentenceCase(row.status) }}
				</BaseBadge>
			</template>
			<template #cell-created_at="{ row }">
				{{ $d(new Date(row.created_at), "longTime").replace("pukul", " • ") }}
			</template>
		</Table>
	</div>
</template>

<style scoped></style>
