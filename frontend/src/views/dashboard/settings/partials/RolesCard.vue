<script setup>
	import { onMounted, ref } from "vue";
	import api from "@/services/api.js";
	import Preloader from "@/components/Preloader.vue";
	import BaseButton from "@/components/base/BaseButton.vue";

	const roles = ref([]);
	const loading = ref(false);
	const fetchRoles = async (params = {}) => {
		loading.value = true;
		try {
			const response = await api.get("/api/roles", params);
			roles.value = response.data;
		} finally {
			loading.value = false;
		}
	};

	onMounted(() => {
		fetchRoles();
	});

	const handleAddRole = () => {
		alert("Soon...");
	};
</script>

<template>
	<div class="card">
		<div class="card-header">
			<p class="m-0 fw-semibold">
				{{ $t("columns.roles") }}
			</p>
		</div>

		<div class="card-body p-0">
			<template v-if="loading">
				<div class="p-3">
					<Preloader variant="spin" />
					<span class="ms-2">{{ $t("common.loading") }}</span>
				</div>
			</template>
			<template v-else>
				<ul class="list-group list-group-flush">
					<li v-for="role in roles" :key="role.id" class="list-group-item">
						{{ role.name }}
					</li>
				</ul>
			</template>
		</div>

		<div class="card-footer">
			<BaseButton
				variant="primary"
				:disabled="loading"
				size="sm"
				@click="handleAddRole"
			>
				<i class="bi bi-plus-square me-1"></i>
				{{ $t("common.addNew") }}
			</BaseButton>
		</div>
	</div>
</template>
