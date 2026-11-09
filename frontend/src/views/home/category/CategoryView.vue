<script setup>
	import { onMounted, ref } from "vue";
	import api from "@/services/api.js";

	const categories = ref([]);
	const fetch = async () => {
		try {
			const response = await api.get("/api/products/category");
			categories.value = response.data;
			console.log(response);
		} catch (error) {
			console.error(error);
		}
	};

	onMounted(() => {
		fetch();
	});
</script>

<template>
	<div class="big-banner">
		<div class="container-lg">
			<h3 class="display-3">{{ $t("category.title") }}</h3>
		</div>
	</div>
	<div class="container-lg">
		<div class="row">
			<div class="col-12">
				<div class="d-flex flex-wrap align-items-center gap-4">
					<router-link
						v-for="category in categories"
						:key="category.id"
						class="card hover-lifted text-decoration-none cursor-pointer"
						:to="{ name: 'category.show', params: { slug: category.slug } }"
					>
						<div class="card-body">
							<h3 class="display-5 px-4">
								{{ category.name }}
							</h3>
						</div>
					</router-link>
				</div>
			</div>
		</div>
	</div>
</template>
