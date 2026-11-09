<script setup>
import { ref } from "vue";
import { useSearchStore } from "@/stores/search.js";
import SearchBox from "@/components/home/search/SearchBox.vue";
import SearchDropdown from "@/components/home/search/SearchDropdown.vue";

import { useDebounceFn, onClickOutside } from "@vueuse/core";

const searchStore = useSearchStore();

const input = ref(null);
const wrapper = ref(null);

onClickOutside(wrapper, () => {
	searchStore.close();
});

const search = useDebounceFn(async () => {
	await searchStore.search(searchStore.query);
}, 320);
</script>

<template>
	<div class="container">
		<div class="row align-items-center justify-content-center">
			<div class="col-md-8">
				<div ref="wrapper" class="position-relative">
					<div class="mb-1 position-relative">
						<input
							ref="input"
							class="form-control"
							type="search"
							:placeholder="$t('search.placeholder')"
							aria-label="Search"
							v-model="searchStore.query"
							@focus="searchStore.open()"
							@input="search"
							autocomplete="search"
							name="search"
							required
						/>
						<i class="bi bi-search bi-magnifier"></i>
					</div>
					<SearchDropdown />
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
input {
	padding-left: 3.5rem;
	padding-top: 1rem;
	padding-bottom: 1rem;
	white-space: nowrap;
	text-overflow: ellipsis;
}
.bi-magnifier {
	position: absolute;
	top: 50%;
	left: 2rem;
	font-size: 24px;
	transform: translate(-50%, -50%);
}
</style>
