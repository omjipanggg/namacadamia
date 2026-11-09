<script setup>
	import { useTable } from "../functions/useTable";
	const tableState = useTable();
</script>

<template>
	<div class="input-group">
		<span class="input-group-text">
			<i class="bi bi-search"></i>
		</span>
		<input
			id="search"
			v-model="tableState.state.search"
			type="search"
			class="form-control"
			autocomplete="off"
			:placeholder="$t('search.title')"
			name="search"
		/>
	</div>
</template>

<style scoped></style>
