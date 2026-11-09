<script setup>
	import { useId, computed, ref } from "vue";

	defineOptions({ inheritAttrs: false });

	const model = defineModel({ type: [String, Number], default: "" });

	const props = defineProps({
		label: { type: String, default: "" },
		type: { type: String, default: "text" },
		error: { type: String, default: "" },
		helpText: { type: String, default: "" },
		plainText: { type: Boolean, default: false },
		id: { type: String, default: () => useId() },
	});
</script>

<template>
	<div class="list-group">
		<button type="button" class="list-group-item list-group-item-action">
			A first button item
		</button>
		<button type="button" class="list-group-item list-group-item-action">
			A second button item
		</button>
		<button type="button" class="list-group-item list-group-item-action">
			A third button item
		</button>
		<button
			type="button"
			class="list-group-item list-group-item-action active"
			aria-current="true"
		>
			The current button item
		</button>
		<button
			type="button"
			class="list-group-item list-group-item-action"
			disabled
		>
			A disabled button item
		</button>
	</div>
</template>
