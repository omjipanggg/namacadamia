<script setup>
	import { onMounted, ref } from "vue";
	import { useTimeAgoIntl } from "@vueuse/core";
	import { useI18n } from "vue-i18n";
	import { useRoute, useRouter } from "vue-router";
	import Preloader from "@/components/Preloader.vue";
	import BaseCard from "@/components/base/BaseCard.vue";

	import api from "@/services/api.js";

	const { locale } = useI18n();

	const route = useRoute();
	const router = useRouter();

	const product = ref({});
	const loading = ref(false);

	const fetch = async () => {
		loading.value = true;

		try {
			const response = await api.get(`/api/products/${route.params.slug}`);
			product.value = response.data;
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
			<h3 class="display-3">
				{{ $t("products.single") }}
			</h3>
		</div>
	</div>
	<div class="container-lg">
		<div class="row">
			<div class="col-12">
				<div v-if="loading" class="min-vh-100">
					<Preloader />
				</div>
				<BaseCard>
					<div
						class="d-flex align-items-start justify-content-start gap-3 flex-wrap"
					>
						<img
							loading="lazy"
							:src="product.image_url"
							alt=""
							class="img-fluid"
						/>
						<div class="product-details d-flex flex-column align-items-start">
							<div>
								<h3 class="m-0">{{ product.name }}</h3>
								<p class="text-muted">{{ product.description }}</p>
								<h3 class="display-5">
									{{ product.formatted_price }}
								</h3>
							</div>

							<div class="d-flex gap-3 mt-5 mb-3 flex-wrap align-items-center">
								<div
									v-for="sku in product.skus"
									:key="sku.id"
									class="border py-2 hover-lifted px-3"
								>
									{{ sku.code }}
								</div>
							</div>

							<div class="mt-auto">
								{{ $t("time.lastUpdated") }}:
								{{ useTimeAgoIntl(product.updated_at, { locale }) }}
							</div>
						</div>
					</div>
				</BaseCard>
			</div>
		</div>
	</div>
</template>

<style scoped>
	.product-details {
		min-height: 300px;
	}
</style>
