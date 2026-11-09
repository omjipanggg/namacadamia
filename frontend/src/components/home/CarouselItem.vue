<script setup>
	defineProps({
		active: Boolean,
		hasContent: Boolean,
	});
</script>

<template>
	<div class="carousel-item" :class="{ active }">
		<svg
			aria-hidden="true"
			class="bd-placeholder-img"
			height="100%"
			preserveAspectRatio="xMidYMid slice"
			width="100%"
			xmlns="http://www.w3.org/2000/svg"
		>
			<rect width="100%" height="100%" fill="var(--bs-secondary-color)"></rect>
		</svg>
		<div v-if="hasContent" class="container">
			<div class="carousel-caption text-center">
				<h1>Example headline.</h1>
				<p class="opacity-75">
					Some representative placeholder content for the first slide of the
					carousel.
				</p>
			</div>
		</div>
	</div>
</template>

<style scoped></style>
