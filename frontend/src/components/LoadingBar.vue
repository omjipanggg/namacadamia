<script setup>
	import { storeToRefs } from "pinia";
	import { useLoadingStore } from "@/stores/loading.js";

	const loadingStore = useLoadingStore();
	const { loading, progress } = storeToRefs(loadingStore);
</script>

<template>
	<div
		v-show="loading"
		class="progress position-fixed top-0 start-0 w-100 rounded-0"
		role="progressbar"
		aria-label="Animated striped example"
		aria-valuemin="0"
		aria-valuemax="100"
		:aria-valuenow="progress"
		style="height: 6px"
	>
		<div
			class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
			:style="{ width: `${progress}%` }"
		></div>
	</div>
</template>
