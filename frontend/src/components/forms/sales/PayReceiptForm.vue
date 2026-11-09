<script setup>
	import { formatMoney } from "@/helpers/format.js";
	defineProps({
		loading: {
			type: Boolean,
			default: false,
		},
		receipt: {
			type: Object,
			default: () => ({}),
		},
	});
</script>

<template>
	<div class="table-responsive border rounded-0">
		<table class="table table-borderless m-0">
			<tbody>
				<template v-if="loading">
					<tr v-for="n in 3" :key="n" class="placeholder-glow">
						<td><span class="placeholder col-12"></span></td>
						<td><span class="placeholder col-12"></span></td>
					</tr>
				</template>
				<template v-else>
					<tr>
						<td>{{ $t("sales.receipt.received") }}</td>
						<td class="text-end">
							{{ formatMoney(receipt.received_amount) }}
						</td>
					</tr>
					<tr>
						<td>{{ $t("sales.receipt.payment") }}</td>
						<td class="text-end">
							{{ formatMoney(receipt.payment_amount) }}
						</td>
					</tr>
					<tr class="fw-bold border-top">
						<td class="py-3 bg-success-subtle text-success-emphasis">
							{{ $t("sales.receipt.change") }}
						</td>
						<td class="py-3 text-end bg-success-subtle text-success-emphasis">
							{{ formatMoney(receipt.change_amount) }}
						</td>
					</tr>
				</template>
			</tbody>
		</table>
	</div>
</template>
