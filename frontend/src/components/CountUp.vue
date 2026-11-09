<script setup>
	import { ref, onMounted, watch } from "vue";
	import { formatMKNumber } from "@/helpers/format.js";
	import { CountUp } from "countup.js";

	const props = defineProps({
		value: {
			type: Number,
			required: true,
		},
		options: {
			type: Object,
			default: () => ({}),
		},
		formatted: {
			type: Boolean,
			default: false,
		},
	});

	const el = ref(null);
	let instance = null;

	onMounted(() => {
		const countUpOptions = {
			...props.options,
			...(props.formatted ? { formattingFn: formatMKNumber } : {}),
		};

		instance = new CountUp(el.value, props.value, countUpOptions);

		if (!instance.error) {
			instance.start();
		} else {
			console.error(instance.error);
		}
	});

	watch(
		() => props.value,
		(newVal) => {
			if (instance) {
				instance.update(newVal);
			}
		}
	);
</script>

<template>
	<span ref="el">0</span>
</template>
