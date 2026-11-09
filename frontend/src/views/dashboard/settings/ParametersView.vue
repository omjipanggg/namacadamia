<script setup>
	import ProductCategoriesCard from "./partials/ProductCategoriesCard.vue";
	import ProductVariantsCard from "./partials/ProductVariantsCard.vue";
	import RolesCard from "./partials/RolesCard.vue";
</script>

<template>
	<div class="col-12">
		<h2>Parameters</h2>
	</div>

	<div class="col-lg-7">
		<ProductCategoriesCard />
	</div>

	<div class="col-lg-5">
		<ProductVariantsCard />
	</div>

	<div class="col-lg-5">
		<RolesCard />
	</div>
</template>
