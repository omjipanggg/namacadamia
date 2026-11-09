<script setup>
	import { reactive } from "vue";
	import api from "@/services/api.js";

	import BaseCheckbox from "@/components/base/BaseCheckbox.vue";
	import BaseInput from "@/components/base/BaseInput.vue";
	import BaseTextarea from "@/components/base/BaseTextarea.vue";

	import { useProductStore } from "@/stores/products.js";
	import { useModal } from "@/composables/modal.js";

	const productStore = useProductStore();
	const { closeModal } = useModal();

	const props = defineProps({
		data: {
			type: Object,
			required: true,
		},
	});

	const baseAttributeForm = () => ({
		name: props.data?.name,
		description: props.data?.description,
		is_active: !!props.data?.is_active,
	});

	const errors = reactive({
		name: null,
	});

	const form = reactive(baseAttributeForm());

	const submit = async () => {
		if (!form.name || form.name.length < 3) {
			errors.name = "errors.products.name";
			return;
		}

		try {
			await api.put(
				`/api/dashboard/product-attributes/${props.data?.id}`,
				form
			);
		} catch (errorObj) {
			errors.name = errorObj?.message;
		} finally {
			productStore.fetchAttributes();
			reset();
			closeModal();
		}
	};

	const reset = () => {
		Object.assign(form, baseAttributeForm());
		Object.assign(errors, { name: null });
	};

	defineExpose({
		reset,
		submit,
	});
</script>

<template>
	<form @submit.prevent="submit">
		<div class="d-flex flex-column gap-3">
			<BaseInput
				v-model="form.name"
				:label="$t('columns.name')"
				:placeholder="$t('products.inputName')"
				:error="errors.name"
				autocomplete="off"
			/>
			<BaseTextarea
				v-model="form.description"
				:label="$t('columns.description')"
				:placeholder="$t('common.optional')"
			/>

			<BaseCheckbox v-model="form.is_active" :label="$t('common.active')" />
		</div>
	</form>
</template>
