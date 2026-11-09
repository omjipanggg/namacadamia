<script setup>
	import Search from "./TableSearch.vue";
	import { useTable } from "../functions/useTable.js";
	const tableState = useTable();
</script>

<template>
	<div
		v-if="tableState.options.searchable"
		class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3"
	>
		<div class="toolbar-header order-2 order-md-1">
			<Search v-if="tableState.options.searchable" />
			<slot name="toolbar-start" />
		</div>
		<div
			class="d-flex flex-row order-1 order-md-2 align-items-stretch align-items-sm-center gap-3"
		>
			<slot name="toolbar-end" />
			<!-- <ButtonGroup /> -->
		</div>
	</div>
</template>
