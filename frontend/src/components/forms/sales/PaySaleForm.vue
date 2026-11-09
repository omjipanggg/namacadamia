<script setup>
	import { reactive, watch } from "vue";
	import BaseSelect from "@/components/base/BaseSelect.vue";
	import BaseInput from "@/components/base/BaseInput.vue";
	import BaseMoneyInput from "@/components/base/BaseMoneyInput.vue";
	import BaseTextarea from "@/components/base/BaseTextarea.vue";

	defineProps({
		lookup: {
			type: Object,
			default: () => ({}),
		},
	});

	const emit = defineEmits(["submit"]);

	const initialForm = () => ({
		payment_method_id: null,
		amount: 0,
		reference: null,
		notes: null,
	});

	const form = reactive(initialForm());

	const errors = reactive({
		amount: null,
		method: null,
	});

	const validated = () => {
		let valid = true;
		if (!form.payment_method_id) {
			errors.method = "errors.sales.paymentMethod";
			valid = false;
		}

		if (form.amount <= 0) {
			errors.amount = "errors.sales.amount";
			valid = false;
		}
		return valid;
	};

	const submit = () => {
		if (!validated()) return;

		emit("submit", form);
	};

	const reset = () => {
		Object.assign(errors, {
			amount: null,
			method: null,
		});
		Object.assign(form, initialForm());
	};

	defineExpose({
		reset,
		submit,
	});

	watch(
		form,
		(values) => {
			if (values.amount) errors.amount = null;
			if (values.payment_method_id) errors.method = null;
		},
		{ deep: true }
	);
</script>

<template>
	<form @submit.prevent="submit">
		<div class="d-flex flex-column gap-3">
			<BaseSelect
				v-model="form.payment_method_id"
				:label="$t('common.method')"
				:error="errors.method"
				:options="lookup.payment_methods"
				:placeholder="$t('select.option', { item: `[${$t('common.method')}]` })"
			/>
			<BaseMoneyInput
				v-model="form.amount"
				:error="errors.amount"
				:label="$t('sales.amount')"
				:placeholder="$t('products.enterAmount')"
				min="0"
				required
			/>

			<BaseInput
				v-model="form.reference"
				type="text"
				:label="$t('sales.reference')"
				:placeholder="$t('common.ifAny')"
			/>
			<BaseTextarea
				v-model="form.notes"
				:label="$t('sales.notes')"
				:placeholder="$t('sales.notes')"
			/>
		</div>
	</form>
</template>
