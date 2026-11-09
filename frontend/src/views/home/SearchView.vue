<script setup>
	import { ref, onMounted } from "vue";
	import { useRoute } from "vue-router";
	import api from "@/services/api.js";

	const route = useRoute();

	const items = ref([]);
	const fetching = ref(false);

	const fetchSearch = async () => {
		fetching.value = true;
		try {
			const response = await api.get("/api/search", {
				query: route.query.query,
			});
			items.value = response;
		} finally {
			fetching.value = false;
		}
	};

	onMounted(() => {
		fetchSearch();
	});
</script>

<template>
	<div class="big-banner">
		<div class="container-lg">
			<h3 class="display-3">{{ $t("search.title") }}</h3>
		</div>
	</div>
	<div class="container-lg mt-5">
		<div class="row">
			<div class="col-12">
				<p class="fw-semibold">{{ route.fullPath }}</p>
			</div>
			<div class="col-12">
				<ul class="list-group list-group-flush">
					<template v-if="fetching">
						<li class="placeholder-glow">
							<span class="placeholder placeholder-sm col-2"></span>
						</li>
						<li class="placeholder-glow">
							<span class="placeholder placeholder-sm col-4"></span>
						</li>
						<li class="placeholder-glow">
							<span class="placeholder placeholder-sm col-3"></span>
						</li>
					</template>
					<template v-else>
						<li v-for="item in items?.resource?.data" :key="item.slug">
							<span class="fw-semibold">{{ item.name }}</span>
							<span style="display: block">{{ item.description }}</span>
						</li>
					</template>
				</ul>
			</div>
		</div>
	</div>
</template>
