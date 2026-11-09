<script setup>
import RouterNavbar from "@/components/home/app/AppNavbar.vue";
import RouterFooter from "@/components/home/app/AppFooter.vue";
</script>

<template>
	<div class="home-layout">
		<div class="home-layout__navbar">
			<router-navbar />
		</div>
		<div class="home-layout__content">
			<router-view />
		</div>
		<div class="home-layout__footer">
			<router-footer />
		</div>
	</div>
</template>

<style scoped></style>
