<script setup>
	import { onMounted, ref } from "vue";
	import { useRoute, useRouter } from "vue-router";
	import api from "@/services/api.js";

	const route = useRoute();
	const router = useRouter();

	const category = ref({});
	const loading = ref(false);

	const fetch = async () => {
		loading.value = true;

		try {
			const response = await api.get(
				`/api/products/category/${route.params.slug}`
			);
			category.value = response.data;
		} catch (error) {
			if (error.response?.status === 404) {
				return router.replace({ name: "unknown.index" });
			}
			console.log("error: ", error);
		} finally {
			loading.value = false;
		}
	};

	onMounted(fetch);
</script>

<template>
	<div class="big-banner">
		<div class="container-lg">
			<h3 class="display-3">{{ $t("category.title") }} #{{ category.id }}</h3>
		</div>
	</div>
	<div class="container-lg">
		<div class="row">
			<div class="col-12">
				<pre>{{ category }}</pre>
			</div>
		</div>
	</div>
</template>
