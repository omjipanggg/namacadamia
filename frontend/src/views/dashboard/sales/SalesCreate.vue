<script setup>
	import { ref, watch } from "vue";
	import api from "@/services/api.js";
	import { useRouter } from "vue-router";
	import { onMounted } from "vue";
	import { useI18n } from "vue-i18n";
	import { useToast } from "@/composables/toast.js";
	import { useLookupStore } from "@/stores/lookup.js";

	import SaleForm from "@/components/forms/sales/CreateForm.vue";
	import BaseAlert from "@/components/base/BaseAlert.vue";
	import BaseButton from "@/components/base/BaseButton.vue";

	const { t } = useI18n();
	const toast = useToast();
	const router = useRouter();
	const lookup = useLookupStore();
	const formRef = ref(null);
	const errors = ref({});
	const saving = ref(false);

	const alertState = ref({
		show: false,
		message: [],
		variant: "danger",
	});

	const resetSale = () => {
		alertState.value.show = false;
		formRef.value?.reset();
	};

	const saveSale = async () => {
		await formRef.value?.submit();
	};

	const handleStore = async (form) => {
		errors.value = null;
		saving.value = true;
		try {
			const response = await api.post("/api/sales", form);

			toast.success(
				t("notifications.title"),
				t("sales.created", { code: `#${response?.data.code}` })
			);
			router.push({
				name: "sales.index",
			});
		} catch (error) {
			errors.value = error.response?.data;
		} finally {
			saving.value = false;
		}
	};

	onMounted(() => {
		lookup.load(
			[
				"sales_channels",
				"stock_locations",
				"customers",
				"branches",
				"skus",
				"units",
				"stocks",
			],
			true
		);
	});

	watch(errors, (newErrors) => {
		if (!newErrors) return;

		const getErrorMessage = (response) => response?.message;

		alertState.value = {
			show: true,
			message: getErrorMessage(newErrors),
			variant: "danger",
		};
	});
</script>

<template>
	<div class="col-12">
		<div
			class="d-flex flex-wrap gap-3 align-items-start justify-content-between"
		>
			<h2 class="mb-4">{{ $t("sales.title") }}</h2>
			<div class="d-flex gap-2">
				<BaseButton variant="danger" :disabled="saving" @click="resetSale">
					{{ $t("common.reset") }}
					<i class="bi bi-arrow-repeat"></i>
				</BaseButton>
				<BaseButton variant="primary" :loading="saving" @click="saveSale">
					{{ $t("common.save") }}
					<i class="bi bi-floppy ms-2"></i>
				</BaseButton>
			</div>
		</div>

		<BaseAlert
			v-model:show="alertState.show"
			:message="alertState.message"
			:variant="alertState.variant"
			:dismissible="true"
			class="mb-4"
		/>

		<SaleForm
			ref="formRef"
			:errors="errors"
			:lookup="lookup"
			@save="handleStore"
		/>
	</div>
</template>
