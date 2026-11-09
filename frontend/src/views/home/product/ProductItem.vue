<script setup>
	import { ref } from "vue";
	import Ribbon from "@/components/Ribbon.vue";

	const liked = ref(false);

	defineProps({
		products: {
			type: Array,
			required: true,
		},
	});
</script>

<template>
	<div
		v-for="product in products"
		:key="product.id"
		class="product-item hover-lifted position-relative overflow-hidden ribbon-container"
	>
		<Ribbon v-if="false" />
		<RouterLink
			:to="{ name: 'products.show', params: { slug: product.slug } }"
			class="add-to-cart"
			><i class="bi bi-box-arrow-up-right"></i
		></RouterLink>
		<img
			loading="lazy"
			:src="product.image_url"
			:alt="product.slug"
			class="img-fluid"
		/>
		<div
			class="product-details p-3 d-flex align-items-center justify-content-between"
		>
			<div class="left-group">
				<p class="m-0">{{ product.name }}</p>
				<p class="big-price">
					<strong>{{ product.formatted_price }}</strong>
				</p>
			</div>
			<div
				class="d-flex align-items-center justify-content-center gap-3 right-group"
			>
				<a href="#" class="text-secondary icon-cart">
					<i class="bi bi-cart-plus"></i>
				</a>
				<a href="#" class="text-danger icon-heart">
					<i :class="['bi', `bi-${liked ? 'heart-fill' : 'heart'}`]"></i>
				</a>
			</div>
		</div>
	</div>
</template>

<style scoped>
	.product-item img {
		width: 100%;
		transition: all 120ms ease-in-out;
	}

	.product-details .big-price {
		font-size: 1.2rem;
		margin-bottom: 0;
	}

	.icon-heart i::before,
	.icon-cart i::before,
	.add-to-cart i:before {
		font-size: 24px !important;
	}

	.add-to-cart {
		position: absolute;
		background-color: var(--bs-body-bg);
		padding: 0.75rem;
		border-radius: 50%;
		top: 50%;
		left: -50px;
		z-index: 24;
		transform: translate(-50%, -120%);
		transition: all 240ms ease;
	}
	.add-to-cart:hover,
	.add-to-cart:focus {
		cursor: pointer;
		box-shadow: var(--bs-box-shadow) !important;
	}

	.product-item:hover .add-to-cart {
		left: 50%;
	}
</style>
