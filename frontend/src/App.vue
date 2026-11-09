<script setup>
	import { RouterView } from "vue-router";
	import ResponseToast from "@/components/toast/ResponseToast.vue";
	import ConfirmationModal from "@/components/modal/ConfirmationModal.vue";
	import MultiModal from "@/components/modal/MultiModal.vue";
	import LoadingBar from "@/components/LoadingBar.vue";
</script>

<template>
	<RouterView />
	<ResponseToast />
	<ConfirmationModal />
	<MultiModal />
	<LoadingBar />
</template>

<style scoped></style>
