<script setup>
	import { formatMKNumber } from "@/helpers/format.js";
	import BaseCard from "@/components/base/BaseCard.vue";
	defineProps({
		summary: {
			type: [Array, Object, String],
			default: null,
		},
	});
</script>

<template>
	<div class="col-md-6 col-xl-3">
		<BaseCard class="h-100">
			<small class="text-muted small">{{ $t("sales.gross") }}</small>
			<p class="m-0 fs-4 fw-semibold">
				Rp{{ formatMKNumber(summary?.gross_sales ?? 0) }}
			</p>
		</BaseCard>
	</div>
	<div class="col-md-6 col-xl-3">
		<BaseCard class="h-100">
			<small class="text-muted small">{{ $t("tables.discounts") }}</small>
			<p class="m-0 fs-4 fw-semibold">
				Rp{{ formatMKNumber(summary?.discounts ?? 0) }}
			</p>
		</BaseCard>
	</div>
	<div class="col-md-6 col-xl-3">
		<BaseCard class="h-100">
			<small class="text-muted small">{{ $t("sales.net") }}</small>
			<p class="m-0 fs-4 fw-semibold">
				Rp{{ formatMKNumber(summary?.net_sales ?? 0) }}
			</p>
		</BaseCard>
	</div>
	<div class="col-md-6 col-xl-3">
		<BaseCard class="h-100">
			<small class="text-muted small">{{ $t("orders.total") }}</small>
			<p class="m-0 fs-4 fw-semibold">
				{{ summary?.orders_count ?? 0 }}
			</p>
		</BaseCard>
	</div>
</template>
