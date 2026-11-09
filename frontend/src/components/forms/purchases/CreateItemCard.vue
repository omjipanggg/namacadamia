<script setup>
	import CreateItemRow from "@/components/forms/purchases/CreateItemRow.vue";

	const items = defineModel({
		type: Array,
		default: () => [],
	});

	defineProps({
		heading: {
			type: Boolean,
			default: true,
		},
		lookup: {
			type: Object,
			default: () => ({}),
		},
		excludedIds: {
			type: Array,
			default: () => [],
		},
	});

	const emit = defineEmits(["add", "remove"]);

	function updateItem(index, value) {
		items.value[index] = value;
	}
</script>

<template>
	<div class="card">
		<div class="card-header">
			<div
				class="d-flex justify-content-between align-items-center flex-wrap gap-1"
			>
				<p class="m-0 fw-semibold">{{ $t("purchases.items") }}</p>
				<button
					type="button"
					class="btn btn-sm btn-primary"
					@click="emit('add')"
				>
					<i class="bi bi-plus-square me-2"></i>
					{{ $t("common.addNew") }}
				</button>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive border rounded">
				<table class="table table-hover mb-0">
					<thead v-if="!heading">
						<tr>
							<th scope="col" style="min-width: 64px" class="text-center">#</th>
							<th scope="col" style="min-width: 210px">
								{{ $t("tables.sku") }}
							</th>
							<th scope="col" style="min-width: 100px">Qty</th>
							<th scope="col" style="min-width: 150px">
								{{ $t("tables.unit") }}
							</th>
							<th scope="col" style="min-width: 200px">
								[@] {{ $t("tables.costPrice") }}
							</th>
							<th scope="col" style="min-width: 200px">
								{{ $t("tables.total") }}
							</th>
						</tr>
					</thead>
					<tbody>
						<CreateItemRow
							v-for="(item, index) in items"
							:key="item.id"
							:item="item"
							:lookup="lookup"
							:excluded-ids="
								excludedIds.filter((id) => id !== item.product_sku_id)
							"
							@update:item="updateItem(index, $event)"
							@remove="emit('remove', item.id)"
						/>

						<tr
							v-if="!items.length"
							class="cursor-pointer border rounded"
							@click="emit('add')"
						>
							<td colspan="6" class="text-center text-muted py-4">
								<i class="bi bi-cart-plus d-block display-3 mb-2"></i>
								{{ $t("tables.clickToAddItem") }}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</template>

<style scoped>
	th {
		padding-left: 14px !important;
	}
</style>
