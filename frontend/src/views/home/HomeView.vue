<script setup>
	import Services from "@/components/home/ServicesSection.vue";
	import Footer from "@/components/home/FooterSection.vue";
	import Featured from "@/components/home/FeaturedSection.vue";
	import FAQ from "@/components/home/FaqSection.vue";
	import Carousel from "@/components/home/CarouselSection.vue";
	import GlobalSearch from "@/components/home/search/GlobalSearch.vue";
</script>

<template>
	<Carousel />
	<GlobalSearch />
	<Featured />
	<Services />
	<FAQ />
	<Footer />
</template>

<style scoped></style>
