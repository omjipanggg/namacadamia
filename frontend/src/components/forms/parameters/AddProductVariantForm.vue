<script setup>
	import { reactive } from "vue";
	import api from "@/services/api.js";

	import BaseInput from "@/components/base/BaseInput.vue";
	import BaseTextarea from "@/components/base/BaseTextarea.vue";

	import { useProductStore } from "@/stores/products.js";
	import { useModal } from "@/composables/modal.js";

	const productStore = useProductStore();
	const { closeModal } = useModal();

	const baseAttributeForm = () => ({
		name: null,
		description: null,
		is_active: false,
		values: [],
	});

	const errors = reactive({
		name: null,
	});

	const form = reactive(baseAttributeForm());

	const submit = async () => {
		if (!form.name) {
			errors.name = "errors.products.name";
			return;
		}

		try {
			await api.post("/api/dashboard/product-attributes", form);
		} finally {
			productStore.fetchAttributes();
			reset();
			closeModal();
		}
	};

	const reset = () => {
		Object.assign(form, baseAttributeForm());
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
		</div>
	</form>
</template>
