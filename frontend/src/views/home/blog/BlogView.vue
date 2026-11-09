<script setup>
	import { onMounted, computed, ref } from "vue";
	import { useTimeAgoIntl } from "@vueuse/core";
	import { useI18n } from "vue-i18n";
	import BaseCard from "@/components/base/BaseCard.vue";
	import api from "@/services/api.js";

	const { locale } = useI18n();
	const resource = ref({});
	const loading = ref(false);

	const fetch = async (params = {}) => {
		loading.value = true;
		try {
			const response = await api.get("/api/articles", params);
			resource.value = response;
		} finally {
			loading.value = false;
		}
	};

	const articles = computed(() => {
		const { data = [], links = [], meta = {}, ...others } = resource.value;
		return { data, links, meta, ...others };
	});

	onMounted(fetch);
</script>

<template>
	<div class="big-banner">
		<div class="container-lg">
			<h3 class="display-3">{{ $t("blog.title") }}</h3>
		</div>
	</div>
	<div class="container-lg">
		<div class="row">
			<template v-if="loading">
				<div class="col-12">
					<div class="d-flex flex-column gap-4">
						<BaseCard v-for="n in 4" :key="n">
							<template #header>
								<div
									class="d-flex align-items-center justify-content-between flex-wrap"
								>
									<span class="placeholder col-5 col-lg-3"></span>
									<span class="placeholder col-lg-1 col-3"></span>
								</div>
							</template>
							<div class="placeholder-glow">
								<span class="placeholder col-6 me-2"></span>
								<span class="placeholder col-3"></span>
								<span class="placeholder col-3 me-2"></span>
								<span class="placeholder col-5"></span>
								<span class="placeholder col-7"></span>
							</div>
						</BaseCard>
					</div>
				</div>
			</template>
			<template v-else>
				<div class="col-12">
					<div class="d-flex flex-column gap-4">
						<RouterLink
							v-for="article in articles.data"
							:key="article.id"
							:to="{
								name: 'blog.show',
								params: { slug: article.slug },
							}"
							class="card hover-lifted text-decoration-none cursor-pointer"
						>
							<div class="card-header">
								<div
									class="d-flex align-items-center justify-content-between flex-wrap"
								>
									<small class="small text-muted">{{ article.name }}</small>
									<small>{{
										useTimeAgoIntl(article.created_at, { locale })
									}}</small>
								</div>
							</div>
							<div class="card-body">
								<p>
									{{ article.content }}
								</p>
							</div>
						</RouterLink>
					</div>
				</div>
				<div class="col-12">
					<div class="d-flex align-items-center justify-content-center">
						<nav aria-label="Pagination" class="mt-4">
							<ul class="pagination">
								<li :class="['page-item', { disabled: !articles.links.prev }]">
									<button
										class="page-link"
										:disabled="!articles.links.prev"
										@click="fetch(articles.links.prev)"
									>
										<i class="bi bi-chevron-left"></i>
									</button>
								</li>
								<li class="page-item disabled" aria-label="Page">
									<span class="page-link fw-semibold">
										{{ articles.meta.current_page }}
										/
										{{ articles.meta.last_page }}
									</span>
								</li>
								<li :class="['page-item', { disabled: !articles.links.next }]">
									<button
										class="page-link"
										:disabled="!articles.links.next"
										@click="fetch(articles.links.next)"
									>
										<i class="bi bi-chevron-right"></i>
									</button>
								</li>
							</ul>
						</nav>
					</div>
				</div>
			</template>
		</div>
	</div>
</template>
