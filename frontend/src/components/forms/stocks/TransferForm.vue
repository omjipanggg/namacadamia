<script setup>
	import { reactive, ref, onMounted, watch } from "vue";
	import { variantLabel } from "@/helpers/format.js";
	import { useStockStore } from "@/stores/stocks.js";
	import { useBranchStore } from "@/stores/branches.js";
	import { useModal } from "@/composables/modal.js";

	import BaseCheckbox from "@/components/base/BaseCheckbox.vue";
	import BaseInput from "@/components/base/BaseInput.vue";
	import BaseSelect from "@/components/base/BaseSelect.vue";
	import BaseTextarea from "@/components/base/BaseTextarea.vue";

	const props = defineProps({
		lookup: {
			type: Object,
			default: () => ({}),
		},
		loading: { type: Boolean, default: false },
		stock: {
			type: Object,
			default: () => ({}),
		},
	});

	const { closeModal } = useModal();
	const transferStore = useStockStore();
	const branchStore = useBranchStore();

	const initialForm = () => ({
		source_location_id: null,
		target_location_id: null,
		notes: null,
		auto: true,
		items: [
			{
				product_sku_id: null,
				quantity: 0,
			},
		],
	});

	const form = reactive(initialForm());

	const errors = ref({});

	const emit = defineEmits(["submit"]);

	watch(
		() => props.stock,
		(newStock) => {
			form.source_location_id = newStock?.location.id;
			form.items[0].product_sku_id = newStock?.sku.id;
			form.items[0].quantity = newStock?.quantity_available;
		},
		{ deep: true, immediate: true }
	);

	const submit = async () => {
		errors.value = {};
		try {
			const payload = {
				source_location_id: form.source_location_id,
				target_location_id: form.target_location_id,
				notes: form.notes || null,
				auto: form.auto,
				items: form.items.map((item) => ({
					product_sku_id: item.product_sku_id,
					quantity: item.quantity,
				})),
			};

			await transferStore.createTransfer(payload);
		} catch (error) {
			errors.value = error.response?.data?.errors ?? {};
		} finally {
			branchStore.fetchStock(form.source_location_id);
			closeModal();
		}

		emit("submit", form);
	};

	const reset = () => {
		Object.assign(form, initialForm());
	};

	defineExpose({ submit, reset });

	onMounted(() => {});
</script>

<template>
	<form @submit.prevent="submit">
		<div class="row g-3">
			<div class="col-12">
				<span class="form-label">{{ $t("products.single") }}</span>
				<div class="group">
					<h3 class="m-0 fw-semibold text-truncate">
						{{ stock.sku.product.name }}
					</h3>
					<span class="m-0 text-muted">{{
						variantLabel(stock.sku.variants)
					}}</span>
				</div>
			</div>
			<div class="col-12">
				<BaseInput v-model="form.source_location_id" type="hidden" />
				<BaseSelect
					v-model="form.target_location_id"
					:options="
						lookup.stock_locations.filter(
							(option) => option.value !== form.source_location_id
						)
					"
					:label="$t('common.targetLocation')"
					:placeholder="$t('select.placeholder')"
					:show-subtitle="false"
					:error="errors?.target_location_id?.[0]"
					:loading="loading"
				/>
			</div>
			<div class="col-12">
				<BaseInput
					v-model="form.items[0].quantity"
					type="number"
					:label="$t('tables.quantity')"
					:placeholder="$t('products.enterQuantity')"
					:max="stock?.quantity_available"
				/>
			</div>
			<div class="col-12">
				<BaseTextarea
					v-model="form.notes"
					:label="$t('sales.notes')"
					:placeholder="$t('common.optional')"
				/>
			</div>
			<div class="col-12">
				<BaseCheckbox
					v-model="form.auto"
					:disabled="form.auto ?? true"
					:label="$t('purchases.autoReceive')"
				/>
			</div>
		</div>
	</form>
</template>
