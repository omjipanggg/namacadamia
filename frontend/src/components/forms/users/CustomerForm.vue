<script setup>
	import { reactive, computed } from "vue";
	import { useModal } from "@/composables/modal.js";
	import BaseInput from "@/components/base/BaseInput.vue";
	import api from "@/services/api.js";

	const { closeModal } = useModal();

	const props = defineProps({
		onCreated: {
			type: Function,
			default: () => {},
		},
	});

	const form = reactive({
		name: "",
		phone: "",
		email: "",
	});

	const canSubmit = computed(() => {
		return form.name.trim() !== "" && form.phone.trim() !== "";
	});

	const submit = async () => {
		if (!canSubmit.value) return;

		try {
			const response = await api.post("/api/customers", form);
			props.onCreated?.(response.data);
		} catch (error) {
			console.error(error);
		} finally {
			reset();
			closeModal();
		}
	};

	const reset = () => {
		Object.keys(form).forEach((key) => {
			form[key] = "";
		});
	};

	defineExpose({
		reset,
		submit,
	});
</script>

<template>
	<form @submit.prevent="submit">
		<div class="mb-3">
			<BaseInput
				v-model="form.name"
				:label="$t('columns.name')"
				:placeholder="$t('products.inputName')"
			/>
		</div>
		<div class="mb-3">
			<BaseInput
				v-model="form.phone"
				type="tel"
				:label="$t('columns.phoneNumber')"
				placeholder="6281806060979"
			/>
		</div>
		<div>
			<BaseInput
				v-model="form.email"
				:label="$t('common.email')"
				placeholder="email@example.com"
			/>
		</div>
	</form>
</template>
