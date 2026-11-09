<script setup>
	import { computed } from "vue";
	import getNestedValue from "../utils/getNestedValue.js";
	import resolveCellValue from "../utils/resolveCellValue.js";
	import { useLoadingStore } from "@/stores/loading.js";
	import { useTable } from "../functions/useTable.js";

	const loadingStore = useLoadingStore();
	const tableState = useTable();

	const colspan = computed(() => {
		const baseLength = tableState.data.columns?.length || 0;
		return tableState.options.selectable ? baseLength + 1 : baseLength;
	});
</script>

<template>
	<tbody>
		<template v-if="loadingStore.loading">
			<tr v-for="n in 3" :key="n">
				<!-- <td :colspan="colspan" class="text-center py-4 text-muted">{{ $t("common.loading") }}</td> -->
				<td
					v-for="column in colspan"
					:key="column.key || column"
					class="placeholder-glow"
				>
					<span class="placeholder col-12"></span>
				</td>
			</tr>
		</template>
		<template v-else-if="tableState.data.rows.length === 0">
			<tr>
				<td :colspan="colspan" class="text-center py-4 text-muted">
					<i class="bi bi-info-circle display-3 mb-2 d-block"></i>
					{{ $t("tables.noData") }}
				</td>
			</tr>
		</template>
		<template v-else>
			<tr v-for="row in tableState.data.rows" :key="row.id">
				<td v-if="tableState.options.selectable" class="text-center">
					<input
						type="checkbox"
						class="form-check-input"
						:checked="tableState.state.selected.has(row.id)"
						:value="row.id"
						@change="tableState.actions.toggleSelection(row.id)"
					/>
				</td>
				<td
					v-for="column in tableState.data.columns"
					:key="column.key"
					:class="column.cellClass"
					scope="row"
				>
					<slot
						:name="`cell-${column.key}`"
						:row="row"
						:column="column"
						:value="resolveCellValue(row, column)"
						:raw="getNestedValue(row, column.key)"
					>
						{{ resolveCellValue(row, column) }}
					</slot>
				</td>
			</tr>
		</template>
	</tbody>
</template>

<style scoped></style>
