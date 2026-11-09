<script setup>
	import BaseCard from "@/components/base/BaseCard.vue";

	defineProps({
		icon: { type: String, required: true },
		label: { type: String, required: true },
		title: { type: String, default: "" },
		subtitle: { type: String, default: "" },
		loading: { type: Boolean, default: false },
		hasVariants: { type: Boolean, default: false },
	});

	const variants = ["primary", "success", "dark", "danger"];
	const choose = (array) => array[Math.floor(Math.random() * array.length)];
</script>

<template>
	<div class="col-xl-3 col-md-6">
		<BaseCard
			class="card-info position-relative"
			:class="hasVariants ? `bg-${choose(variants)}-subtle` : ''"
			:hover="false"
		>
			<div
				class="d-flex align-items-end gap-3"
				:class="{ 'placeholder-glow': loading }"
			>
				<div class="w-100 text-truncate">
					<p class="fw-semibold m-0">{{ $t(label) }}</p>

					<h5 class="m-0 text-truncate" :title="title || 'N/A'">
						<span v-if="loading" class="placeholder col-9"></span>
						<template v-else>{{ title }}</template>
					</h5>

					<small class="text-truncate">
						<span
							v-if="loading"
							class="placeholder placeholder-sm col-6"
						></span>
						<template v-else>{{ subtitle }}</template>
					</small>
				</div>
				<i :class="[`bi bi-${icon}`, 'fs-4']"></i>
			</div>
		</BaseCard>
	</div>
</template>
