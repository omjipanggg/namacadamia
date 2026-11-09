<script setup>
	import { computed } from "vue";
	import { useTable } from "../functions/useTable";
	import { PER_PAGE_OPTIONS } from "../utils/variables";

	const tableState = useTable();

	const perPage = computed({
		get: () => tableState.state.perPage,
		set: (value) => tableState.actions.setPerPage(Number(value)),
	});
</script>

<template>
	<select
		id="length"
		v-model="perPage"
		name="length"
		class="form-select w-auto"
	>
		<option v-for="option in PER_PAGE_OPTIONS" :key="option" :value="option">
			{{ option }}
		</option>
	</select>
</template>

<style scoped></style>
