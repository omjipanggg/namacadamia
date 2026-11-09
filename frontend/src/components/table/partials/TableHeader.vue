<script setup>
	import { watch, ref } from "vue";
	import { useTable } from "@/components/table/functions/useTable.js";
	const tableState = useTable();

	const headerCheckBox = ref(null);

	watch(
		() => tableState.meta.partiallySelected,
		(value) => {
			if (headerCheckBox.value) {
				headerCheckBox.value.indeterminate = value;
			}
		},
		{
			immediate: true,
		}
	);
</script>

<template>
	<thead>
		<tr>
			<th
				v-if="tableState.options.selectable"
				class="text-center"
				style="width: 40px"
			>
				<input
					ref="headerCheckBox"
					type="checkbox"
					class="form-check-input"
					:checked="tableState.meta.allVisibleSelected"
					@change="tableState.actions.toggleVisibleSelection"
				/>
			</th>
			<th
				v-for="column in tableState.data.columns"
				:key="column.key"
				:class="[column.headerClass, { 'cursor-pointer': column.sortable }]"
				scope="col"
				:style="`width:${column.width}px;`"
				@click="column.sortable && tableState.actions.setSort(column.key)"
			>
				<div class="d-flex align-items-center justify-content-between">
					{{ column.label }}
					<span v-if="column.sortable">
						<span v-if="tableState.state.sortBy !== column.key"> ↕ </span>
						<span v-else-if="tableState.state.sortDirection === 'asc'">↓</span>
						<span v-else>↑</span>
					</span>
				</div>
			</th>
		</tr>
	</thead>
</template>
