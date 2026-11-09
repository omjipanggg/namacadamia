<script setup>
	import { onMounted } from "vue";
	import { useRoute } from "vue-router";
	import { usePurchaseStore } from "@/stores/purchases.js";
	import { storeToRefs } from "pinia";

	import BaseCard from "@/components/base/BaseCard.vue";

	const route = useRoute();
	const purchaseStore = usePurchaseStore();

	const { purchaseDetail, loading, error } = storeToRefs(purchaseStore);

	onMounted(() => {
		purchaseStore.show(route.params.code);
	});
</script>

<template>
	<div class="col-12">
		<h2 class="mb-4">{{ $t("purchases.title") }}</h2>

		<BaseCard :hover="false">
			<pre>{{ purchaseDetail }}</pre>
		</BaseCard>
	</div>
</template>
