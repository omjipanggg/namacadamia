<script setup>
	import { onMounted, ref } from "vue";
	import { useRouter } from "vue-router";
	import { useToast } from "@/composables/toast.js";
	import { useLookupStore } from "@/stores/lookup.js";
	import { useI18n } from "vue-i18n";
	import api from "@/services/api.js";
	import BaseButton from "@/components/base/BaseButton.vue";
	import PurchaseForm from "@/components/forms/purchases/CreateForm.vue";

	const { t } = useI18n();
	const router = useRouter();
	const lookup = useLookupStore();
	const toast = useToast();

	const formRef = ref(null);
	const errors = ref({});
	const saving = ref(false);

	const handleStore = async (form) => {
		saving.value = true;
		try {
			const response = await api.post("/api/purchases", form);
			toast.success(
				t("notifications.title"),
				t("purchases.created", { code: `#${response?.data.code}` })
			);

			router.push({
				name: "dashboard.purchases.index",
			});
		} catch (error) {
			errors.value = error.response?.data;
		} finally {
			saving.value = false;
		}
	};

	const resetPurchase = () => {
		formRef.value?.reset();
	};

	async function savePurchase() {
		await formRef.value?.submit();
	}

	onMounted(() => {
		lookup.load(["branches", "suppliers", "units", "skus"]);
	});
</script>

<template>
	<div class="col-12">
		<div
			class="d-flex flex-wrap gap-3 align-items-start justify-content-between"
		>
			<h2 class="mb-4">{{ $t("purchases.title") }}</h2>
			<div class="d-flex gap-2">
				<BaseButton variant="danger" :disabled="saving" @click="resetPurchase">
					{{ $t("common.reset") }}
					<i class="bi bi-arrow-repeat"></i>
				</BaseButton>
				<BaseButton variant="primary" :loading="saving" @click="savePurchase">
					{{ $t("common.save") }}
					<i class="bi bi-floppy ms-2"></i>
				</BaseButton>
			</div>
		</div>
		<PurchaseForm
			ref="formRef"
			:lookup="lookup"
			:errors="errors"
			@save="handleStore"
		/>
	</div>
</template>
