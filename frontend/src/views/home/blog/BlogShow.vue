<script setup>
	import { onMounted, ref } from "vue";
	import { formatDate } from "@/helpers/format.js";
	import { useRoute, useRouter } from "vue-router";
	import api from "@/services/api.js";

	const route = useRoute();
	const router = useRouter();

	const article = ref({});
	const loading = ref(false);

	const fetch = async () => {
		loading.value = true;

		try {
			const response = await api.get(`/api/articles/${route.params.slug}`);
			article.value = response.data;
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
			<h3 class="display-3">{{ $t("blog.show") }}</h3>
		</div>
	</div>
	<div class="container-lg">
		<div class="row gx-4 gy-3">
			<div class="col-lg-4">
				<div class="list-group">
					<button type="button" class="list-group-item list-group-item-action">
						A first button item
					</button>
					<button type="button" class="list-group-item list-group-item-action">
						A second button item
					</button>
					<button type="button" class="list-group-item list-group-item-action">
						A third button item
					</button>
					<button
						type="button"
						class="list-group-item list-group-item-action active"
						aria-current="true"
					>
						The current button item
					</button>
					<button
						type="button"
						class="list-group-item list-group-item-action"
						disabled
					>
						A disabled button item
					</button>
				</div>
			</div>
			<div class="col-lg-8" :class="{ 'placeholder-glow': loading }">
				<h2
					class="display-6 text-truncate"
					:class="{ 'placeholder col-8': loading }"
				>
					{{ article.name }}
				</h2>
				<div class="meta d-flex my-3 flex-column">
					<template v-if="loading">
						<div class="placeholder col-5"></div>
						<div class="mb-1"></div>
						<div class="placeholder col-4"></div>
					</template>
					<template v-else>
						<small class="small text-muted">
							<i class="bi bi-clock me-2"></i>
							{{ formatDate(article.created_at ?? null) }}
						</small>
						<small class="small text-muted">
							<i class="bi bi-person me-2"></i>
							{{ article.author?.name }} (<a
								:href="`mailto:${article.author?.email}`"
								class="text-decoration-dotted text-reset"
								>{{ article.author?.email }}</a
							>)
						</small>
					</template>
				</div>
				<template v-if="loading">
					<div class="placeholder-glow">
						<span class="placeholder col-7 me-2"></span>
						<span class="placeholder col-4"></span>
						<span class="placeholder col-4 me-2"></span>
						<span class="placeholder col-6"></span>
						<span class="placeholder col-8"></span>
					</div>
				</template>
				<template v-else>
					<p>{{ article.content }}</p>
				</template>
			</div>
		</div>
	</div>
</template>
