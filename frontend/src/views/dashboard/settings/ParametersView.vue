<script setup>
	import ProductCategoriesCard from "./partials/ProductCategoriesCard.vue";
	import ProductVariantsCard from "./partials/ProductVariantsCard.vue";
	import RolesCard from "./partials/RolesCard.vue";
</script>

<template>
	<div class="col-12">
		<h2>Parameters</h2>
	</div>

	<div class="col-12">
		<div class="row g-3">
			<div class="col-lg-8">
				<ProductCategoriesCard />
			</div>
			<div class="col-lg-4">
				<RolesCard />
			</div>
		</div>
	</div>

	<div class="col-12">
		<ProductVariantsCard />
	</div>
</template>
