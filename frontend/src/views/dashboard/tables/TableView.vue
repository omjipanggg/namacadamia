<script setup>
	import { ref, onMounted } from "vue";
	import api from "@/services/api.js";
	import Preloader from "@/components/Preloader.vue";

	const tables = ref([]);
	const loading = ref(false);

	const fetchTables = async () => {
		loading.value = true;
		try {
			const response = await api.get("/api/tables");
			tables.value = response;
		} finally {
			loading.value = false;
		}
	};

	onMounted(fetchTables);

	const buttonClasses = [
		"btn-primary",
		// "btn-warning",
		// "btn-light",
		"btn-success",
		"btn-danger",
		// "btn-info",
		"btn-dark",
	];
</script>

<template>
	<div class="col-12">
		<h3 class="mb-4">{{ $t("tables.title") }}</h3>
		<div v-if="loading" class="d-flex gap-2 align-items-center">
			<Preloader variant="border" />
			{{ $t("common.fetching") }}
		</div>
		<div
			v-else
			class="d-flex align-items-center gap-3 flex-wrap justify-content-start"
		>
			<RouterLink
				v-for="(table, index) in tables"
				:key="table.id"
				type="button"
				:to="{ name: 'dashboard.tables.show', params: { slug: table } }"
				class="btn border flex-grow-1 text-start"
				:class="buttonClasses[index % buttonClasses.length]"
				>{{ table }}</RouterLink
			>
		</div>
	</div>
</template>

<style scoped></style>
