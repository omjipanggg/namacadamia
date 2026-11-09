<script setup>
	import { onMounted, computed, ref, nextTick } from "vue";
	import { useRoute, useRouter } from "vue-router";
	import { storeToRefs } from "pinia";
	import { formatMoney } from "@/helpers/format.js";
	import { useSaleStore } from "@/stores/sales.js";
	import { useLookupStore } from "@/stores/lookup.js";

	import InfoStatCard from "@/components/InfoStatCard.vue";

	import BaseBadge from "@/components/base/BaseBadge.vue";
	import BaseButton from "@/components/base/BaseButton.vue";
	import BaseCard from "@/components/base/BaseCard.vue";

	import FormModal from "@/components/modal/FormModal.vue";
	import PaySaleForm from "@/components/forms/sales/PaySaleForm.vue";
	import PayReceiptForm from "@/components/forms/sales/PayReceiptForm.vue";

	import SaleReceipt from "@/components/prints/SaleReceipt.vue";

	const route = useRoute();
	const lookup = useLookupStore();
	const saleStore = useSaleStore();

	const { saleDetail, receipt, loading, error } = storeToRefs(saleStore);

	const STATUS_CONFIG = {
		completed: { icon: "bi-check-circle", variant: "success" },
		paid: { icon: "bi-check-circle", variant: "success" },
		pending: { icon: "bi-clock", variant: "warning" },
		partial: { icon: "bi-info-circle", variant: "info" },
		cancelled: { icon: "bi-x-circle", variant: "danger" },
	};

	const getStatusConfig = (status) => {
		return STATUS_CONFIG[status] || { icon: "bi-x-circle", variant: "danger" };
	};

	const stats = computed(() => [
		{
			icon: "person-square",
			label: "customer.title",
			title: saleDetail.value?.customer?.name || "N/A",
			subtitle: saleDetail.value?.customer?.email || "N/A",
		},
		{
			icon: "shop",
			label: "branches.single",
			title: saleDetail.value?.branch?.name || "N/A",
			subtitle: saleDetail.value?.branch?.code || "N/A",
		},
		{
			icon: "wallet2",
			label: "sales.channel",
			title: saleDetail.value?.channel?.name || "N/A",
			subtitle: saleDetail.value?.channel?.description || "N/A",
		},
		{
			icon: "laptop",
			label: "sales.cashier",
			title: saleDetail.value?.created_by?.name || "N/A",
			subtitle: saleDetail.value?.created_by?.email || "N/A",
		},
	]);

	const router = useRouter();
	const redirectToCreate = () => {
		router.push({
			name: "sales.create",
		});
	};

	const redirectToView = () => {
		router.push({
			name: "sales.index",
		});
	};

	onMounted(() => {
		saleStore.show(route.params.code);
		lookup.load(["branches", "sales_channels", "payment_methods"], true);
	});

	const paymentForm = ref(null);
	const showFinalModal = ref(false);
	const showPaymentModal = ref(false);

	const remainingTotal = computed(() => {
		const grandTotal = Number(saleDetail.value?.grand_total) || 0;

		const amountsPaid =
			saleDetail.value?.payments?.reduce((total, payment) => {
				return total + (Number(payment.received_amount) || 0);
			}, 0) ?? 0;

		return grandTotal - amountsPaid;
	});

	const handlePaymentSubmit = async (form) => {
		try {
			await saleStore.addPayment(saleDetail.value?.code, form);

			await nextTick();

			showPaymentModal.value = false;
			showFinalModal.value = true;
		} catch (error) {
			console.error(error);
		} finally {
			saleStore.show(route.params.code);
		}
	};

	const completeSale = async () => {
		const confirmed = confirm("Are you sure you want to complete this sale?");

		if (!confirmed) return;

		try {
			await saleStore.complete(saleDetail.value?.code);
		} catch (errorData) {
			console.error(errorData);
		} finally {
			setTimeout(() => {
				redirectToView();
			}, 120);
		}
	};

	const triggerSubmit = () => {
		if (paymentForm.value) {
			paymentForm.value.submit();
		}
	};

	const triggerReset = () => {
		if (paymentForm.value) {
			error.value = null;
			paymentForm.value.reset();
		}
	};

	const handlePrint = () => {
		window.print();
	};
</script>

<template>
	<Teleport defer to="#btnAddOnNavbar">
		<BaseButton
			type="button"
			variant="outline-secondary"
			@click="redirectToCreate"
		>
			<i class="bi bi-plus-square me-2"></i>
			{{ $t("common.addNew", { item: $t("sales.title") }) }}
		</BaseButton>
	</Teleport>

	<SaleReceipt v-if="saleDetail" :sale="saleDetail" :direct="false" />

	<div class="col-12">
		<BaseCard :hover="false">
			<div
				class="d-flex align-items-start py-4 justify-content-between gap-3 flex-wrap"
			>
				<div class="sale-header-left flex-grow-1">
					<template v-if="loading || !saleDetail">
						<div class="d-block w-100">
							<p class="placeholder-glow mb-2">
								<span class="placeholder placeholder-3 col-4"></span>
							</p>
							<p class="placeholder-glow mb-0">
								<span class="placeholder placeholder-lg col-3"></span>
							</p>
						</div>
					</template>
					<template v-else>
						<div class="d-flex flex-wrap gap-3 align-items-center mb-2">
							<h3 class="mb-0 order-2 order-sm-1">{{ saleDetail.code }}</h3>
							<BaseBadge
								class="order-1 rounded-pill order-sm-2"
								:variant="getStatusConfig(saleDetail.status).variant"
							>
								<i
									:class="[
										'bi',
										getStatusConfig(saleDetail.status).icon,
										'me-2',
									]"
								></i>
								{{ $t(`status.${saleDetail.status}`) }}
							</BaseBadge>
						</div>
						<p class="m-0 text-muted">
							<i class="bi bi-calendar-week me-2"></i>
							{{ $d(saleDetail.created_at, "longTime").replace("pukul", "·") }}
						</p>
					</template>
				</div>
				<div class="sale-header-right">
					<div class="d-flex gap-3 flex-wrap">
						<BaseButton
							variant="light"
							class="border px-3"
							:disabled="loading"
							@click="handlePrint"
						>
							<i class="bi bi-printer"></i>
							<span class="d-none ms-3 d-md-inline"
								>{{ $t("common.view") }} / {{ $t("common.print") }}
							</span>
						</BaseButton>
						<div class="dropdown">
							<BaseButton
								variant="light"
								type="button"
								data-bs-toggle="dropdown"
								aria-expanded="false"
								class="border dropdown-toggle px-3"
								:disabled="loading"
							>
								{{ $t("common.more") }}
							</BaseButton>
							<ul class="dropdown-menu dropdown-menu-end">
								<li>
									<a class="dropdown-item" href="#">
										<i class="bi bi-pencil-square me-2"></i>
										{{ $t("common.edit") }}
									</a>
								</li>
								<li>
									<a class="dropdown-item" href="#">
										<i class="bi bi-copy me-2"></i>
										{{ $t("common.duplicate") }}</a
									>
								</li>
								<li>
									<a class="dropdown-item" href="#">
										<i class="bi bi-arrow-repeat me-2"></i>
										{{ $t("sales.return") }}
									</a>
								</li>
								<li class="dropdown-divider"></li>
								<li>
									<a class="dropdown-item text-danger" href="#">
										<i class="bi bi-x-circle me-2"></i>
										{{ $t("common.cancel2") }}</a
									>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<template v-if="saleDetail && saleDetail.status === 'paid'" #footer>
				<div class="px-3 py-2 d-flex justify-content-end gap-3 flex-wrap">
					<BaseButton
						:loading="loading"
						variant="success"
						@click="completeSale"
					>
						{{ $t("sales.complete") }}
						<i class="bi bi-arrow-right ms-2"></i>
					</BaseButton>
				</div>
			</template>
		</BaseCard>
	</div>

	<InfoStatCard
		v-for="stat in stats"
		:key="stat.label"
		:icon="stat.icon"
		:label="stat.label"
		:title="stat.title"
		:subtitle="stat.subtitle"
		:has-variants="false"
		:loading="loading"
		:error="error"
	/>

	<div class="col-lg-12">
		<BaseCard :hover="false">
			<template #header>
				<div
					class="d-flex align-items-center justify-content-between gap-3 flex-wrap"
				>
					<p class="m-0 fw-semibold">
						<i class="bi bi-cart me-2"></i>
						{{ $t("common.items") }}
					</p>
				</div>
			</template>

			<div class="table-responsive rounded">
				<table class="table table-hover m-0">
					<thead>
						<tr>
							<th>#</th>
							<th>SKU</th>
							<th>{{ $t("products.single") }}</th>
							<th>{{ $t("tables.quantity") }}</th>
							<th>{{ $t("tables.unit") }}</th>
							<th>{{ $t("tables.price") }}</th>
							<th>{{ $t("tables.discount") }}</th>
							<th>{{ $t("tables.subtotal") }}</th>
						</tr>
					</thead>
					<tbody>
						<template v-if="saleDetail">
							<tr
								v-for="(item, index) in saleDetail.items"
								:key="item.id"
								class="align-middle"
							>
								<td>{{ index + 1 }}</td>
								<td>{{ item.sku?.code || "N/A" }}</td>
								<td>
									<div>
										{{ item.product_name }}
									</div>
									<small class="small text-muted">
										{{
											item.sku?.variants
												?.map((variant) => variant.value)
												.join(" • ")
										}}
									</small>
								</td>
								<td>{{ item.quantity }}</td>
								<td>
									{{ item.measure_unit?.name || "N/A" }} ({{
										item.measure_unit?.ratio || "-"
									}})
								</td>
								<td>{{ formatMoney(item.price) }}</td>
								<td>{{ formatMoney(item.discount) }}</td>
								<td>{{ formatMoney(item.subtotal) }}</td>
							</tr>
						</template>
						<template v-else>
							<tr v-for="n in 3" :key="n" class="placeholder-glow">
								<td><span class="placeholder col-12"></span></td>
								<td><span class="placeholder col-12"></span></td>
								<td><span class="placeholder col-12"></span></td>
								<td><span class="placeholder col-12"></span></td>
								<td><span class="placeholder col-12"></span></td>
								<td><span class="placeholder col-12"></span></td>
								<td><span class="placeholder col-12"></span></td>
								<td><span class="placeholder col-12"></span></td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>
		</BaseCard>
	</div>

	<div class="col-lg-4">
		<BaseCard :hover="false">
			<template #header>
				<p class="m-0 fw-semibold">
					<i class="bi bi-journal-text me-2"></i>
					{{ $t("sales.summary") }}
				</p>
			</template>

			<div class="table-responsive border rounded">
				<table class="table table-borderless m-0">
					<tbody>
						<template v-if="saleDetail">
							<tr>
								<td>{{ $t("tables.subtotal") }}</td>
								<td class="text-end">
									{{ formatMoney(saleDetail?.subtotal) }}
								</td>
							</tr>
							<tr>
								<td>{{ $t("tables.discount") }}</td>
								<td class="text-end">
									{{ formatMoney(saleDetail?.sale_discount) }}
								</td>
							</tr>
							<tr>
								<td>{{ $t("sales.tax") }}</td>
								<td class="text-end">{{ formatMoney(saleDetail?.tax) }}</td>
							</tr>
							<tr class="fw-bold border-top">
								<td class="py-3 bg-info-subtle text-info-emphasis">
									{{ $t("tables.total") }}
								</td>
								<td class="py-3 text-end bg-info-subtle text-info-emphasis">
									{{ formatMoney(saleDetail?.grand_total) }}
								</td>
							</tr>
						</template>
						<template v-else>
							<tr v-for="n in 3" :key="n" class="placeholder-glow">
								<td><span class="placeholder col-12"></span></td>
								<td><span class="placeholder col-12"></span></td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>
		</BaseCard>
	</div>

	<div class="col-lg-5">
		<BaseCard :hover="false">
			<template #header>
				<p class="m-0 fw-semibold">
					<i class="bi bi-cash-coin me-2"></i>
					{{ $t("sales.paymentDetail") }}
				</p>
			</template>

			<template v-if="saleDetail?.payments && saleDetail?.payments.length">
				<div class="table-responsive rounded">
					<table class="table m-0 table-hover">
						<thead>
							<tr>
								<th>{{ $t("date.payment") }}</th>
								<th>{{ $t("common.method") }}</th>
								<th class="text-end">{{ $t("sales.amount") }}</th>
							</tr>
						</thead>
						<tbody>
							<template v-if="saleDetail">
								<tr v-for="payment in saleDetail?.payments" :key="payment.id">
									<td>
										{{ $d(payment.paid_at, "dateOnly") }} •
										{{ $d(payment.paid_at, "timeOnly") }}
									</td>
									<td>{{ payment.payment_method.name }}</td>
									<td class="text-end">
										{{ formatMoney(payment.received_amount) }}
									</td>
								</tr>
								<template v-if="saleDetail?.payments.length > 0">
									<tr v-if="remainingTotal > 0">
										<td></td>
										<td>{{ $t("tables.subtotal") }}</td>
										<td class="text-end">
											<div
												v-for="payment in saleDetail?.payments"
												:key="payment.id"
											>
												{{ formatMoney(payment.payment_amount) }}
											</div>
										</td>
									</tr>
									<tr>
										<td></td>
										<td>
											{{
												remainingTotal < 0
													? $t("sales.receipt.change")
													: $t("sales.remaining")
											}}
										</td>
										<td class="text-end">{{ formatMoney(remainingTotal) }}</td>
									</tr>
								</template>
							</template>
							<template v-else>
								<tr v-for="n in 3" :key="n" class="placeholder-glow">
									<td><span class="placeholder col-12"></span></td>
									<td><span class="placeholder col-12"></span></td>
									<td><span class="placeholder col-12"></span></td>
								</tr>
							</template>
						</tbody>
					</table>
				</div>
			</template>
			<template v-else>
				<p class="m-0" :class="{ 'placeholder-glow': loading }">
					<span :class="{ 'placeholder col-7': loading }">
						{{ $t("sales.noPayments") }}
					</span>
				</p>
			</template>
			<template
				v-if="
					!saleDetail?.payments?.length === 0 ||
					saleDetail?.status === 'partial' ||
					saleDetail?.status === 'pending'
				"
				#footer
			>
				<BaseButton
					variant="primary"
					:disabled="loading"
					@click="showPaymentModal = true"
				>
					{{ $t("sales.addPayment") }}
					<i class="bi bi-arrow-right ms-2"></i>
				</BaseButton>
			</template>
		</BaseCard>
	</div>

	<div class="col-lg-3">
		<BaseCard :hover="false">
			<template #header>
				<p class="m-0 fw-semibold">
					<i class="bi bi-stickies me-2"></i>
					{{ $t("purchases.notes") }}
				</p>
			</template>

			<p class="m-0" :class="{ 'placeholder-glow': loading }">
				<span :class="{ 'placeholder col-7': loading }">
					{{ saleDetail?.notes || $t("tables.noData") + "." }}
				</span>
			</p>
		</BaseCard>
	</div>

	<FormModal
		v-model="showFinalModal"
		size="md"
		:title="$t('sales.receipt.title')"
		:loading="loading"
		:center-footer="true"
	>
		<PayReceiptForm :receipt="receipt?.transaction" :loading="loading" />
		<template #footer>
			<BaseButton
				variant="primary"
				:disabled="loading"
				@click="showFinalModal = false"
			>
				{{ $t("common.okay") }}
			</BaseButton>
		</template>
	</FormModal>

	<FormModal
		v-model="showPaymentModal"
		size="md"
		:title="$t('sales.addPayment')"
		:loading="loading"
		:overflow-visible="true"
	>
		<PaySaleForm
			ref="paymentForm"
			:lookup="lookup"
			@submit="handlePaymentSubmit"
		/>
		<template #footer>
			<BaseButton variant="danger" :disabled="loading" @click="triggerReset">
				{{ $t("common.reset") }}
				<i class="bi bi-arrow-repeat ms-2"></i>
			</BaseButton>
			<div class="d-flex gap-2 flex-wrap ms-auto">
				<BaseButton
					variant="secondary"
					:disabled="loading"
					@click="showPaymentModal = false"
				>
					{{ $t("common.cancel") }}
					<i class="bi bi-x-lg ms-2"></i>
				</BaseButton>
				<BaseButton
					variant="primary"
					:loading="loading"
					:disabled="loading"
					@click="triggerSubmit"
				>
					{{ $t("common.save") }}
					<i class="bi bi-floppy ms-2"></i>
				</BaseButton>
			</div>
		</template>
	</FormModal>
</template>

<style scoped>
	.placeholder-3 {
		min-height: 2rem !important;
	}

	.card-info i {
		width: 16px;
	}
</style>
