<script setup>
	import { computed, reactive, watch } from "vue";
	import { useRouter } from "vue-router";
	import BaseTextarea from "@/components/base/BaseTextarea.vue";
	import BaseLookupSelect from "@/components/base/BaseLookupSelect.vue";
	import BaseInput from "@/components/base/BaseInput.vue";

	const props = defineProps({
		stock: { type: Object, default: null },
		lookup: { type: Object, default: () => ({}) },
		loading: { type: Boolean, default: false },
	});

	const initialForm = () => ({
		stock_location_id: null,
		product_sku_id: null,
		type: "adjustment",
		quantity: 1,
		notes: "",
	});

	const form = reactive(initialForm());
	const errors = reactive({
		quantity: 0,
		notes: null,
	});

	const emit = defineEmits(["submit"]);

	const currentQuantity = computed(() => {
		return props.stock?.quantity ?? 0;
	});

	const finalQuantity = computed(() => {
		return currentQuantity.value + (Number(form.quantity) || 0);
	});

	const isInvalid = computed(() => {
		return Number(form.quantity) === 0 || finalQuantity.value < 0;
	});

	const canSubmit = computed(() => {
		return !props.loading && !isInvalid.value && form.notes.trim() !== "";
	});

	const reset = () => {
		Object.assign(form, initialForm());
	};

	const populateInitials = () => {
		reset();
		form.stock_location_id = props.stock?.location?.id ?? null;
		form.product_sku_id = props.stock?.sku?.id ?? null;
	};

	const router = useRouter();
	const taggable = {
		label: "SKU",
		action: () => router.push({ name: "dashboard.products.index" }),
	};

	const submit = () => {
		if (!canSubmit.value) {
			errors.notes = true;
			return;
		}

		emit("submit", form);
	};

	defineExpose({ reset, populateInitials, submit });

	watch(
		() => props.stock,
		(newStock) => {
			form.stock_location_id = newStock?.location.id ?? null;
			form.product_sku_id = newStock?.sku.id ?? null;
		},
		{ deep: true }
	);
</script>

<template>
	<form @submit.prevent="submit">
		<div class="row g-3">
			<div class="col-12">
				<template v-if="props.stock">
					<span class="form-label">{{ $t("products.single") }}</span>
					<div class="group">
						<h3 class="m-0 fw-semibold text-truncate">
							{{ props.stock.sku.product.name }}
						</h3>
						<span class="text-muted">{{
							props.stock.sku.variants.map((item) => item.value).join(" • ")
						}}</span>
					</div>
				</template>
				<template v-else>
					<BaseLookupSelect
						v-model="form.product_sku_id"
						label="SKU"
						:items="props.lookup.skus"
						:placeholder="$t('select.option', { item: `[SKU]` })"
						:taggable="taggable"
						@action="(item) => item.action()"
					/>
				</template>
			</div>
			<div class="col-12">
				<template v-if="props.stock">
					<span class="form-label">{{ $t("branches.single") }}</span>
					<h3 class="m-0 fw-semibold text-truncate">
						{{ props.stock.location.name }}
					</h3>
				</template>
				<template v-else>
					<BaseLookupSelect
						v-model="form.stock_location_id"
						:label="$t('branches.title')"
						:items="props.lookup.stock_locations"
						:placeholder="$t('select.one')"
					/>
				</template>
			</div>
			<div v-if="props.stock" class="col-12">
				<span class="form-label">{{ $t("stocks.title") }}</span>
				<h5 class="display-6 m-0 fw-semibold">
					{{ props.stock.quantity_available }}
				</h5>
			</div>
			<div class="col-12">
				<BaseInput
					v-model.number="form.quantity"
					type="number"
					step="1"
					:label="$t('tables.quantity')"
					:error="isInvalid ? 'errors.stocks.invalid' : ''"
					:disabled="loading"
					:help-text="$t('stocks.helpText')"
				/>
			</div>
			<div class="col-12">
				<BaseTextarea
					v-model="form.notes"
					:label="$t('purchases.notes')"
					:placeholder="$t('common.required')"
					:error="
						errors.notes
							? $t('errors.fieldRequired', { field: $t('purchases.notes') })
							: ''
					"
				/>
			</div>
		</div>
	</form>
</template>
