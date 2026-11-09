<script setup>
import { computed } from "vue";
import { useSearchStore } from "@/stores/search.js";

import SearchGroup from "./SearchGroup.vue";
import SearchItem from "./SearchItem.vue";
import { RouterLink } from "vue-router";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const searchStore = useSearchStore();

const groups = computed(() => searchStore.groupedResults);
</script>

<template>
	<div v-if="searchStore.opened" class="dropdown-menu py-0 d-block shadow-sm">
		<div v-if="searchStore.loading" class="p-3 text-center">
			<div class="d-flex justify-content-center">
				<div class="spinner-border" role="status">
					<span class="visually-hidden">{{ $t("loading") }}...</span>
				</div>
			</div>
		</div>

		<div
			v-if="searchStore.isEmpty"
			class="p-3 text-center text-muted text-truncate"
		>
			<span>{{ $t("search.noResults") }}</span>
		</div>

		<template v-else>
			<SearchGroup
				v-for="(items, type) in groups"
				:key="type"
				:title="type"
				:items="items"
			>
				<SearchItem
					v-for="item in items"
					:key="item.id"
					:result="item"
				/>
			</SearchGroup>
			<div
				class="px-3 text-truncate py-2 border-top"
				v-if="searchStore.hasResults"
			>
				<RouterLink
					:to="{
						name: 'search.index',
						query: { query: searchStore.query },
					}"
					class="text-decoration-none"
				>
					{{ $t("search.viewAll") }}
					<i class="bi bi-arrow-right-short"></i>
				</RouterLink>
			</div>
			<div
				class="text-input-placeholder small text-truncate text-muted"
				v-else
			>
				<span>{{ $t("search.input") }}</span>
			</div>
		</template>
	</div>
</template>

<style scoped>
.dropdown-menu {
	width: 100%;
}
.text-input-placeholder {
	padding: 0.5rem 1rem;
}
</style>
