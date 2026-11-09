<script setup>
const props = defineProps({
	result: {
		type: Object,
		required: true,
	},
});

const icons = {
	products: "bi bi-box-seam",
	category: "bi bi-tags",
	blog: "bi bi-journal-text",
};
</script>

<template>
	<RouterLink
		:to="{ name: `${result.type}.show`, params: { slug: result.slug } }"
		class="dropdown-item py-2"
		:data-slug="result.slug"
	>
		<div class="d-flex align-items-center">
			<i :class="icons[result.type]" class="me-2"></i>
			<div class="flex-grow-1">
				<span class="fw-semibold">{{ result.name }}</span>
			</div>
		</div>
		<div class="text-truncate">
			<small v-if="result.description" class="text-muted">{{
				result.description
			}}</small>
		</div>
	</RouterLink>
</template>

<style scoped></style>
