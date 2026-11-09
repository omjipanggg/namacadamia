<script setup>
	import { computed, ref, watchEffect } from "vue";
	import BaseInput from "@/components/base/BaseInput.vue";
	import BaseDatePicker from "@/components/base/BaseDatePicker.vue";
	import BaseTextarea from "@/components/base/BaseTextarea.vue";

	const props = defineProps({
		columns: {
			type: Array,
			default: () => [],
		},
		errors: {
			type: Object,
			default: () => ({}),
		},
		lookup: {
			type: Object,
			default: () => ({}),
		},
	});

	const excludedKeys = ["id", "created_at", "updated_at", "deleted_at"];
	const filteredColumns = computed(() => {
		return props.columns.filter((column) => !excludedKeys.includes(column.key));
	});

	const form = ref({});

	watchEffect(() => {
		const initialForm = {};

		props.columns
			.filter((column) => !excludedKeys.includes(column.key))
			.forEach((column) => {
				if (["date", "datetime", "timestamp"].includes(column.type)) {
					const now = new Date();
					now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
					initialForm[column.key] = now.toISOString().slice(0, 16);
				} else {
					initialForm[column.key] = "";
				}
			});

		form.value = initialForm;
	});

	const emit = defineEmits(["save"]);

	const submit = async () => {
		emit("save", form.value);
	};

	defineExpose({
		submit,
	});
</script>

<template>
	<div v-for="column in filteredColumns" :key="column.key" class="flex-grow-1">
		<BaseInput
			v-if="
				column.type !== 'timestamp' &&
				column.label !== 'description' &&
				column.type !== 'datetime' &&
				column.label !== 'notes'
			"
			:id="`modal-${column.key}`"
			v-model="form[column.key]"
			:type="column.type.includes('int') ? 'number' : 'text'"
			:label="column.label"
		/>
		<BaseDatePicker
			v-if="column.type === 'timestamp' || column.type === 'datetime'"
			:id="`modal-${column.key}`"
			v-model="form[column.key]"
			:label="column.label"
		/>
		<BaseTextarea
			v-if="column.label === 'description' || column.label === 'notes'"
			:id="`modal-${column.key}`"
			v-model="form[column.key]"
			:label="column.label"
		/>
	</div>
</template>
