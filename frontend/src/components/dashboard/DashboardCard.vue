<script setup>
	import BaseCard from "@/components/base/BaseCard.vue";
	import CountUp from "@/components/CountUp.vue";
	import { useRouter } from "vue-router";

	const router = useRouter();

	defineProps({
		card: {
			type: Object,
			default: () => ({}),
		},
		ribbon: {
			type: Boolean,
			default: false,
		},
		variants: {
			type: Array,
			default: () => ["primary", "success", "warning", "danger"],
		},
	});

	const handleRedirect = (route) => {
		if (!route) return;
		router
			.push({
				name: route,
			})
			.catch((error) => {
				console.warn(error.message);
			});
	};

	const chosenVariant = (array) =>
		array[Math.floor(Math.random() * array.length)];
</script>

<template>
	<BaseCard
		:ribbon="ribbon"
		:clickable="true"
		:class="[card.hasVariants ? `bg-${chosenVariant(variants)}-subtle` : '']"
		@click="handleRedirect(card.route)"
	>
		<div
			v-if="card.icon"
			class="ribbon"
			:class="`bg-${chosenVariant(variants)}`"
		>
			<i class="bi" :class="`bi-${card.icon}`"></i>
		</div>
		<small class="card-title fw-semibold">
			{{ $t(card.title) }}
		</small>
		<h1 class="display-2 p-0 m-0 text-truncate">
			<CountUp :value="card.value" :formatted="card.isFormatted" />
		</h1>
	</BaseCard>
</template>

<style scoped>
	a {
		color: var(--bs-body-color) !important;
		text-decoration: none !important;
	}

	i {
		transition: all 320ms ease-in-out;
	}

	a:hover i,
	a:focus i {
		padding-left: 6px;
	}
</style>
