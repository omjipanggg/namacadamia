<script setup>
	import { ref, onMounted } from "vue";
	import { useToast } from "@/composables/toast.js";
	import { useLookupStore } from "@/stores/lookup.js";
	import { useModalStore } from "@/stores/modal.js";
	import { useAuthStore } from "@/stores/auth.js";
	import { useI18n } from "vue-i18n";
	import api from "@/services/api.js";
	import Master from "@/components/table/TableMaster.vue";
	import FormModal from "@/components/modal/FormModal.vue";
	import PurchaseForm from "@/components/forms/purchases/CreateForm.vue";
	import BaseBadge from "@/components/base/BaseBadge.vue";
	import BaseButton from "@/components/base/BaseButton.vue";

	const { t } = useI18n();
	const toast = useToast();

	const auth = useAuthStore();
	const lookup = useLookupStore();
	const modal = useModalStore();

	const columns = [
		{
			key: "code",
			label: "Code",
			sortable: true,
		},
		{
			key: "items_count",
			label: "SKU",
		},
		{
			key: "items_sum_quantity",
			label: "Qty.",
		},
		{
			key: "formatted_grand_total",
			label: "Total",
		},
		{
			key: "ordered_at",
			label: "Purchase Date",
			sortable: true,
		},
		{
			key: "status",
			label: "Status",
			sortable: true,
		},
	];

	const purchases = ref({});
	const show = ref(false);
	const saving = ref(false);
	const receiving = ref(false);
	const deleting = ref(false);
	const errors = ref({});
	const selectedPurchases = ref([]);
	const thePurchaseForm = ref(null);

	async function requestData(params) {
		await loadPurchases(params);
	}

	async function loadPurchases(params = {}) {
		const response = await api.get("/api/purchases", params);
		purchases.value = response;
	}

	async function onSave(form) {
		saving.value = true;
		try {
			await api.post("/api/purchases", form);
			selectedPurchases.value = [];
			await resetPurchase();
			await loadPurchases();
			show.value = false;
		} catch (error) {
			errors.value = error.response?.data;
		} finally {
			saving.value = false;
		}
	}

	async function receivePurchase(purchase) {
		if (!purchase) return;

		const confirmed = await modal.open({
			title: t("common.confirm"),
			message: t("purchases.receive", { code: `#${purchase.code}` }),
			confirmText: t("common.yes"),
			cancelText: t("common.no"),
		});

		if (!confirmed) return;

		receiving.value = true;
		try {
			await api.post(`/api/purchases/${purchase.code}/receive`);
			selectedPurchases.value = [];
			await loadPurchases();
		} finally {
			receiving.value = false;
			toast.success(
				t("notifications.title"),
				t("purchases.received", { code: `#${purchase.code}` })
			);
		}
	}

	async function resetPurchase() {
		await thePurchaseForm.value.reset();
	}

	async function savePurchase() {
		await thePurchaseForm.value.submit();
	}

	async function deletePurchase(purchases) {
		deleting.value = true;
		const confirmed = await modal.open({
			title: t("common.confirm"),
			message: t("purchases.delete", {
				code: `${purchases.length} ${purchases.length > 1 ? "items" : "item"}`,
			}),
			confirmText: t("common.yes"),
			cancelText: t("common.no"),
		});

		if (!confirmed) {
			deleting.value = false;
			return;
		}

		const codes = purchases.map((item) =>
			typeof item === "object" && item !== null ? item.code : item
		);

		try {
			await api.delete(`/api/purchases/bulk-delete`, { codes });
			selectedPurchases.value = [];
			await loadPurchases();
		} catch (error) {
			console.error("Delete: ", error);
		} finally {
			deleting.value = false;
		}

		toast.success(
			t("notifications.title"),
			t("purchases.deleted", {
				code: `${purchases.length} ${purchases.length > 1 ? "items" : "item"}`,
			})
		);
	}

	onMounted(() => {
		loadPurchases();
		lookup.load(["branches", "suppliers", "units", "skus"]);
	});
</script>

<template>
	<div class="col-12">
		<Teleport defer to="#btnAddOnNavbar">
			<BaseButton
				v-if="auth.isAdmin"
				type="button"
				variant="outline-secondary"
				@click="show = true"
			>
				<i class="bi bi-plus-square me-2"></i>
				{{ $t("common.addNew", { item: $t("purchases.title") }) }}
			</BaseButton>
		</Teleport>
		<h2 class="mb-0">{{ $t("purchases.title") }}</h2>
		<Master
			v-model:selected="selectedPurchases"
			:columns="columns"
			:raw="purchases"
			hover
			striped
			selectable
			@request="requestData"
		>
			<template #toolbar-end>
				<div class="d-flex flex-wrap gap-3">
					<BaseButton
						variant="success"
						:loading="receiving"
						:disabled="
							selectedPurchases.length !== 1 ||
							selectedPurchases[0]?.status !== 'draft'
						"
						@click="receivePurchase(selectedPurchases[0])"
					>
						{{ $t("common.receive") }}
						<i class="bi bi-clipboard-check ms-2"></i>
					</BaseButton>
					<BaseButton
						:disabled="selectedPurchases.length === 0"
						:loading="deleting"
						variant="danger"
						@click="deletePurchase(selectedPurchases)"
					>
						{{ $t("common.delete") }}
						<i class="bi bi-trash ms-2"></i>
					</BaseButton>
				</div>
			</template>
			<template #cell-code="{ row }">
				<RouterLink
					:to="{
						name: 'dashboard.purchases.show',
						params: { code: row.code },
					}"
					class="text-decoration-none fw-semibold"
				>
					{{ row.code }}
				</RouterLink>
			</template>
			<template #cell-status="{ row }">
				<BaseBadge
					class="fw-semibold"
					:variant="
						row.status === 'pending'
							? 'warning'
							: row.status === 'completed'
								? 'success'
								: 'danger'
					"
				>
					{{ row.status }}
				</BaseBadge>
			</template>
		</Master>
		<FormModal
			v-model="show"
			:title="$t('common.addNew', { item: $t('purchases.title') })"
			:loading="saving"
			size="xl"
		>
			<PurchaseForm
				ref="thePurchaseForm"
				:lookup="lookup"
				:errors="errors"
				@save="onSave"
			/>
			<template #footer>
				<BaseButton
					type="button"
					variant="secondary"
					:disabled="saving"
					@click="show = false"
				>
					{{ $t("common.cancel") }}
					<i class="bi bi-x-square ms-2"></i>
				</BaseButton>
				<BaseButton
					type="button"
					variant="primary"
					:loading="saving"
					@click="savePurchase"
				>
					{{ $t("common.save") }}
					<i class="bi bi-floppy ms-2"></i>
				</BaseButton>
			</template>
		</FormModal>
	</div>
</template>
