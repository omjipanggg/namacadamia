<script setup>
	defineProps({
		items: {
			type: Array,
			required: true,
		},
	});
</script>

<template>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb m-0">
			<li
				v-for="(item, index) in items"
				:key="index"
				class="breadcrumb-item"
				:class="{ active: index === items.length - 1 }"
				:aria-current="index === items.length - 1 ? 'page' : null"
			>
				<router-link
					v-if="index < items.length - 1 && item.route"
					class="text-decoration-none text-reset"
					:to="item.route"
				>
					{{ $te(item.label) ? $t(item.label) : item.label }}
				</router-link>

				<span v-else>
					{{ $te(item.label) ? $t(item.label) : item.label }}
				</span>
			</li>
		</ol>
	</nav>
</template>
