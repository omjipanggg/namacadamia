<script setup>
import CarouselItem from "./CarouselItem.vue";
</script>
<template>
	<div id="myCarousel" class="carousel slide mb-6" data-bs-ride="carousel">
		<div class="carousel-indicators">
			<button
				type="button"
				data-bs-target="#myCarousel"
				data-bs-slide-to="0"
				class="active"
				aria-label="Slide 1"
				aria-current="true"
			></button>
			<button
				type="button"
				data-bs-target="#myCarousel"
				data-bs-slide-to="1"
				aria-label="Slide 2"
				class=""
			></button>
			<button
				type="button"
				data-bs-target="#myCarousel"
				data-bs-slide-to="2"
				aria-label="Slide 3"
				class=""
			></button>
		</div>
		<div class="carousel-inner">
			<CarouselItem :active="true" :has-content="true" />
			<CarouselItem />
			<CarouselItem />
		</div>
		<button
			class="carousel-control-prev"
			type="button"
			data-bs-target="#myCarousel"
			data-bs-slide="prev"
		>
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="visually-hidden">{{ $t("pagination.previous") }}</span>
		</button>
		<button
			class="carousel-control-next"
			type="button"
			data-bs-target="#myCarousel"
			data-bs-slide="next"
		>
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="visually-hidden">{{ $t("pagination.next") }}</span>
		</button>
	</div>
</template>

<style scoped>
.carousel {
	margin-bottom: 4rem;
}
.carousel-caption {
	bottom: 3rem;
	z-index: 10;
}

.carousel-item {
	height: calc(100vh - 4rem);
}

@media (min-width: 40em) {
	.carousel-caption p {
		margin-bottom: 1.25rem;
		font-size: 1.25rem;
		line-height: 1.4;
	}
}
</style>
