<script setup>
	import { ref, computed, onMounted } from "vue";
	import Preloader from "@/components/Preloader.vue";
	import ProductItem from "./ProductItem.vue";
	import api from "@/services/api.js";

	const resource = ref({});
	const loading = ref(false);

	const fetchProducts = async (url = "/api/products") => {
		loading.value = true;
		try {
			const response = await api.get(url);
			resource.value = response;
		} catch (error) {
			console.log(error);
		} finally {
			loading.value = false;
		}
	};

	const responses = computed(() => {
		const { data = [], links = [], meta = {}, ...others } = resource.value;
		return { data, links, meta, ...others };
	});

	onMounted(() => {
		fetchProducts();
	});
</script>

<template>
	<div class="big-banner">
		<div class="container-lg">
			<h3 class="display-3">{{ $t("products.title") }}</h3>
		</div>
	</div>
	<div class="container-lg">
		<div class="row">
			<div class="col-12">
				<div v-if="loading" class="min-vh-100">
					<Preloader />
				</div>
				<div v-else>
					<div class="product-container">
						<ProductItem :products="responses.data" />
					</div>
					<div class="d-flex align-items-center justify-content-center">
						<nav aria-label="Pagination" class="mt-5">
							<ul class="pagination">
								<li
									:class="[
										'page-item',
										{
											disabled: !responses.links.prev,
										},
									]"
								>
									<button
										class="page-link"
										:disabled="!responses.links.prev"
										@click="fetchProducts(responses.links.prev)"
									>
										<i class="bi bi-chevron-left"></i>
									</button>
								</li>
								<li class="page-item disabled" aria-label="Page">
									<span class="page-link fw-semibold">
										{{ responses.meta.current_page }}
										/
										{{ responses.meta.last_page }}
									</span>
								</li>
								<li
									:class="[
										'page-item',
										{
											disabled: !responses.links.next,
										},
									]"
								>
									<button
										class="page-link"
										:disabled="!responses.links.next"
										@click="fetchProducts(responses.links.next)"
									>
										<i class="bi bi-chevron-right"></i>
									</button>
								</li>
							</ul>
						</nav>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
	.product-container {
		display: grid;
		grid-template-columns: repeat(1, 1fr);
		grid-column-gap: 2px;
		grid-row-gap: 1.5rem;
	}

	@media (min-width: 576px) {
		.product-container {
			grid-template-columns: repeat(2, 1fr);
		}
	}

	@media (min-width: 768px) {
		.product-container {
			grid-template-columns: repeat(3, 1fr);
		}
	}

	@media (min-width: 1200px) {
		.product-container {
			grid-template-columns: repeat(4, 1fr);
		}
	}
</style>
