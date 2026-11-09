<script setup>
	import BaseCheckbox from "@/components/base/BaseCheckbox.vue";
	import BaseInput from "@/components/base/BaseInput.vue";

	const emit = defineEmits(["submit"]);

	const form = defineModel({
		type: Object,
		required: true,
	});

	const submit = () => {
		emit("submit", form.value.id);
	};

	defineExpose({
		submit,
	});
</script>

<template>
	<form @submit.prevent="submit">
		<div class="d-flex flex-wrap flex-column gap-2">
			<BaseCheckbox v-model="form.is_active" :label="$t('common.active')" />
			<BaseInput
				v-model="form.name"
				:label="$t('common.name')"
				:placeholder="$t('products.inputName')"
				required
			/>
		</div>
	</form>
</template>
