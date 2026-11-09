<script setup>
	const appName = import.meta.env.VITE_APP_TITLE;

	import { computed } from "vue";
	import {
		formatNumber,
		formatDateTimeReceipt,
		variantLabel,
	} from "@/helpers/format.js";

	const props = defineProps({
		direct: {
			type: Boolean,
			default: false,
		},
		sale: {
			type: Object,
			required: true,
		},
	});

	const sumSubtotal = (item, force) => {
		const result = item?.price * item?.quantity * item?.measure_unit.ratio;
		return formatNumber(result, force);
	};

	const itemSubtotals = computed(() => {
		return props.sale?.items?.reduce((sum, item) => {
			return sum + item.price * item.quantity * item.measure_unit.ratio;
		}, 0);
	});

	const sumDiscounts = () => {
		const result =
			Number(props.sale?.sale_discount) + Number(itemDiscounts.value);
		return formatNumber(result, false);
	};

	const itemDiscounts = computed(
		() =>
			props.sale?.items?.reduce(
				(total, item) => total + Number(item.discount ?? 0),
				0
			) ?? 0
	);

	const remainingTotal = computed(() => {
		const grandTotal = Number(props.sale?.grand_total) || 0;

		const amountsPaid =
			props.sale?.payments?.reduce((total, payment) => {
				return total + (Number(payment.received_amount) || 0);
			}, 0) ?? 0;

		return grandTotal - amountsPaid;
	});
</script>

<template>
	<div class="receipt" :class="direct ? 'd-block' : 'd-print-block d-none'">
		<div class="text-center">
			<h2 class="store-name">{{ appName }}</h2>

			<div v-if="sale.branch?.name">
				{{ sale.branch.name }}
			</div>

			<div v-if="sale.branch?.address">
				{{ sale.branch.address }}
			</div>
		</div>

		<div class="divider"></div>

		<div class="receipt-info">
			<div class="row-line">
				<span>{{ $t("sales.receipt.no") }}</span>
				<span>{{ sale.code }}</span>
			</div>

			<div class="row-line">
				<span>{{ $t("date.dateTime") }}</span>
				<span>{{ formatDateTimeReceipt(sale.created_at) }}</span>
			</div>

			<div v-if="sale.customer" class="row-line">
				<span>{{ $t("customer.memberName") }}</span>
				<span>{{ sale.customer.name }}</span>
			</div>

			<div class="row-line">
				<span>{{ $t("sales.cashier") }}</span>
				<span>{{ sale.created_by?.name }}</span>
			</div>
		</div>

		<div class="divider"></div>

		<div class="item">
			<div class="item-detail">
				<span>{{ $t("orders.title") }}</span>
				<span>Qty: {{ sale?.items.length }}</span>
			</div>
		</div>

		<div v-for="item in sale.items" :key="item.id" class="item">
			<div class="item-name">
				{{ item.product_name }}
			</div>

			<div v-if="item.sku?.variants?.length" class="item-variant">
				{{ variantLabel(item.sku.variants) }}
			</div>

			<div class="item-detail">
				<span>
					{{ item.quantity }}
					{{ item.measure_unit?.name ?? "" }}
					×
					{{ formatNumber(item.price, false) }}
				</span>

				<span>
					<!-- {{ formatNumber(item.subtotal, false) }} -->
					{{ sumSubtotal(item, false) }}
				</span>
			</div>

			<div v-if="Number(item.discount) > 0" class="item-discount item-detail">
				<span> {{ $t("tables.discount") }}: </span>
				<span> ({{ formatNumber(item.discount, false) }}) </span>
			</div>
		</div>

		<div class="divider"></div>

		<div class="totals">
			<div class="row-line">
				<span>{{ $t("tables.subtotal") }}</span>
				<span>{{ formatNumber(itemSubtotals, false) }}</span>
			</div>

			<div
				v-if="Number(sale.sale_discount) > 0 || itemDiscounts > 0"
				class="row-line"
			>
				<span>{{ $t("tables.discounts") }}</span>
				<span>({{ sumDiscounts() }})</span>
			</div>

			<div class="row-line">
				<span>{{ $t("sales.tax") }}</span>
				<span>{{ formatNumber(sale.tax, false) }}</span>
			</div>

			<div class="row-line total">
				<strong>{{ $t("sales.receipt.payment") }}</strong>
				<strong>{{ formatNumber(sale.grand_total, false) }}</strong>
			</div>
		</div>

		<div class="divider"></div>

		<div v-if="sale.payments?.length" class="payments">
			<div class="section-title">{{ $t("sales.payment") }}</div>

			<div v-for="payment in sale.payments" :key="payment.id" class="row-line">
				<span>
					{{ payment.payment_method?.name ?? "Payment" }}
				</span>

				<span>
					{{ formatNumber(payment.received_amount, false) }}
				</span>
			</div>

			<div
				v-if="sale?.payments.length > 0 && remainingTotal !== 0"
				class="row-line"
			>
				<span>{{ $t("sales.receipt.change") }}</span>
				<span>{{ formatNumber(remainingTotal) }}</span>
			</div>
		</div>

		<div v-if="sale.notes" class="notes">
			<div class="divider"></div>

			<strong>{{ $t("sales.notes") }}</strong>
			<div>{{ sale.notes }}</div>
		</div>

		<div class="footer">
			<div>{{ $t("greeting.thankYouForPurchase") }}</div>
			<div>{{ $t("greeting.pleaseComeAgain") }}</div>
		</div>
	</div>
</template>

<style scoped>
	.receipt {
		width: 72mm;
		box-sizing: border-box;
		margin: 0 auto;
		padding: 4mm;
		height: 100%;
		font-family: Arial, sans-serif;
		font-size: 12px;
		line-height: 1.35;
		color: #000;
		background: #fff;
	}

	.store-name {
		margin: 0 0 4px;
		font-size: 20px;
		font-weight: 700;
	}

	.text-center {
		text-align: center;
	}

	.divider {
		border-top: 1px dashed #000;
		margin: 8px 0;
	}

	.row-line {
		display: flex;
		justify-content: space-between;
		gap: 8px;
	}

	.item {
		margin-bottom: 8px;
	}

	.item-name {
		font-weight: 700;
	}

	.item-variant {
		font-size: 11px;
	}

	.item-detail {
		display: flex;
		justify-content: space-between;
		gap: 8px;
	}

	.item-discount {
		font-size: 11px;
	}

	.total {
		margin-top: 6px;
		font-size: 15px;
	}

	.section-title {
		margin-bottom: 4px;
		font-weight: 700;
	}

	.notes {
		margin-top: 8px;
	}

	.footer {
		margin-top: 18px;
		text-align: center;
		font-size: 11px;
	}
</style>

<style>
	@media print {
		@page {
			size: 72mm auto;
			margin: 0;
		}

		html,
		body {
			margin: 0 !important;
			padding: 0 !important;
			width: 72mm !important;
			min-width: 72mm !important;
		}

		body * {
			visibility: hidden !important;
		}

		.receipt,
		.receipt * {
			visibility: visible !important;
		}

		.receipt {
			position: absolute !important;
			left: 0 !important;
			top: 0 !important;
			width: 72mm !important;
			margin: 0 !important;
			padding: 4mm !important;
			box-sizing: border-box !important;
		}
	}
</style>
