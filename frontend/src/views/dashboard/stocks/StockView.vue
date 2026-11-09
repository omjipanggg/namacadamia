<script setup>
	import { onMounted, nextTick, ref } from "vue";
	import { useLookupStore } from "@/stores/lookup.js";
	import { useBranchStore } from "@/stores/branches.js";
	import { useStockStore } from "@/stores/stocks.js";
	import { useModal } from "@/composables/modal.js";
	import { useToast } from "@/composables/toast.js";
	import { columns } from "@/resources/stocks.js";

	import BaseAlert from "@/components/base/BaseAlert.vue";
	import BaseButton from "@/components/base/BaseButton.vue";
	import FormModal from "@/components/modal/FormModal.vue";
	import Table from "@/components/table/TableMaster.vue";

	import AdjustmentForm from "@/components/forms/stocks/AdjustmentForm.vue";
	import TransferForm from "@/components/forms/stocks/TransferForm.vue";

	const lookup = useLookupStore();
	const branchStore = useBranchStore();
	const stockStore = useStockStore();
	const { openModal } = useModal();
	const toast = useToast();

	onMounted(async () => {
		await branchStore.fetch();
		await branchStore.fetchStock(selectedBranch.value);
		await lookup.load(["skus", "stock_locations"], true);
	});

	const errors = ref({});
	const selectedBranch = ref(1);
	const selectedStock = ref(null);
	const adjustmentFormRef = ref(null);

	const showAdjustmentModal = ref(false);
	const showTransferModal = ref(false);

	const fetchStock = async (id) => {
		selectedBranch.value = id;
		branchStore.fetchStock(id);
	};

	const openTransferModal = async (stock) => {
		showTransferModal.value = true;
		selectedStock.value = stock;

		openModal(
			TransferForm,
			{ lookup, loading: stockStore.loading, stock },
			{
				title: "stocks.transfer",
				overflowVisible: true,
				footer: true,
			}
		);
	};

	const openAdjustmentModal = async (stock) => {
		showAdjustmentModal.value = true;
		selectedStock.value = stock;

		await nextTick();

		adjustmentFormRef.value.populateInitials();
	};

	const closeAdjustmentModal = () => {
		showAdjustmentModal.value = false;
		selectedStock.value = null;
		// triggerReset();
	};

	const handleAdjustment = async (form) => {
		try {
			await stockStore.adjust(form);

			await nextTick();

			fetchStock(form.stock_location_id);
		} catch (error) {
			errors.value = error.response?.data;
		} finally {
			toast.success("notifications.title", "stocks.adjusted");
			closeAdjustmentModal();
		}
	};

	const triggerReset = async () => {
		await adjustmentFormRef.value?.reset();
	};

	const triggerSubmit = async () => {
		await adjustmentFormRef.value?.submit();
	};
</script>

<template>
	<Teleport defer to="#btnAddOnNavbar">
		<BaseButton variant="outline-secondary" @click="showAdjustmentModal = true">
			<i class="bi bi-database-gear me-2"></i>
			{{ $t("common.adjust") }}
		</BaseButton>
	</Teleport>

	<div class="col-12">
		<h2>{{ $t("stocks.title") }}</h2>

		<BaseAlert
			v-if="!selectedBranch"
			variant="info"
			:dismissible="false"
			class="mt-3"
		>
			{{ $t("info.selectOne") }}
		</BaseAlert>
	</div>

	<div class="col-lg-4">
		<div
			v-if="branchStore.branches.length === 0"
			class="list-group placeholder-glow"
		>
			<button
				v-for="n in 4"
				:key="n"
				type="button"
				class="list-group-item disabled text-start"
			>
				<span class="placeholder placeholder-lg col-5"></span>
				<br />
				<span class="placeholder placeholder-lg col-7"></span>
			</button>
		</div>
		<div v-else class="list-group">
			<button
				v-for="branch in branchStore.branches.data"
				:key="branch.id"
				type="button"
				class="list-group-item list-group-item-action text-truncate"
				:class="{
					'active disabled fw-semibold': selectedBranch === branch.id,
					disabled: branchStore.loading,
				}"
				:ariaCurrent="selectedBranch === branch.id"
				:disabled="branchStore.loading"
				@click="fetchStock(branch.id)"
			>
				<span class="d-block">{{ branch.code }}</span>
				<span>{{ branch.name }} </span>
			</button>
		</div>
	</div>

	<div class="col-lg-8">
		<Table
			:columns="columns"
			:raw="branchStore.stocks"
			hover
			bordered
			:searchable="false"
			:info="false"
			:has-pagination="false"
			:paging="false"
		>
			<template #[`cell-sku.product.name`]="{ row }">
				<span class="fw-semibold d-block m-0">
					{{ row.sku.product.name }}
				</span>
				<small class="text-muted smaller">{{
					row.sku.variants.map((item) => item.value).join(" • ")
				}}</small>
			</template>
			<template #cell-actions="{ row }">
				<BaseButton
					variant="secondary"
					class="me-2 btn-sm"
					@click.stop="openAdjustmentModal(row)"
				>
					<i class="bi bi-pencil-square me-1"></i>
					{{ $t("common.edit") }}
				</BaseButton>
				<BaseButton
					variant="success"
					class="btn-sm"
					@click.stop="openTransferModal(row)"
				>
					<i class="bi bi-arrow-up-right-square me-1"></i>
					{{ $t("stocks.transfer") }}
				</BaseButton>
			</template>
		</Table>
	</div>

	<FormModal
		v-model="showAdjustmentModal"
		:title="$t('stocks.adjustment')"
		size="md"
		@close="closeAdjustmentModal"
	>
		<AdjustmentForm
			ref="adjustmentFormRef"
			:stock="selectedStock"
			:lookup="lookup"
			:loading="branchStore.loading"
			@submit="handleAdjustment"
		/>
		<template #footer>
			<BaseButton
				variant="danger"
				:disabled="branchStore.loading"
				@click="triggerReset"
			>
				{{ $t("common.reset") }}
				<i class="bi bi-arrow-repeat ms-2"></i>
			</BaseButton>
			<div class="d-flex gap-2 flex-wrap ms-auto">
				<BaseButton
					variant="secondary"
					:disabled="branchStore.loading"
					@click="closeAdjustmentModal"
				>
					{{ $t("common.cancel") }}
					<i class="bi bi-x-lg ms-2"></i>
				</BaseButton>
				<BaseButton
					variant="primary"
					:loading="stockStore.loading"
					:disabled="branchStore.loading"
					@click="triggerSubmit"
				>
					{{ $t("common.save") }}
					<i class="bi bi-floppy ms-2"></i>
				</BaseButton>
			</div>
		</template>
	</FormModal>
</template>

<style scoped></style>
