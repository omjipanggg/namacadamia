<script setup>
	import { reactive, computed, watch, ref } from "vue";
	import { formatToInputString } from "@/helpers/format.js";
	import CreateItemRow from "./CreateItemRow.vue";

	import { useModal } from "@/composables/modal.js";
	import CustomerForm from "@/components/forms/users/CustomerForm.vue";

	import BaseCard from "@/components/base/BaseCard.vue";
	import BaseLookupInput from "@/components/base/BaseLookupInput.vue";
	import BaseSelect from "@/components/base/BaseSelect.vue";
	import BaseTextarea from "@/components/base/BaseTextarea.vue";
	import BaseCheckbox from "@/components/base/BaseCheckbox.vue";
	import BaseInput from "@/components/base/BaseInput.vue";
	// import BaseButton from "@/components/base/BaseButton.vue";

	const { openModal } = useModal();

	const props = defineProps({
		errors: {
			type: Object,
			default: () => ({}),
		},
		lookup: {
			type: Object,
			default: () => ({
				customers: [],
			}),
		},
	});

	const newCustomer = ref([]);
	const customers = computed(() => [
		...(props.lookup?.customers ?? []),
		...newCustomer.value,
	]);

	const emit = defineEmits(["save"]);

	const createInitialForm = () => ({
		customer_id: null,
		branch_id: null,
		sales_channel_id: null,
		ordered_at: formatToInputString(),
		discount: 0,
		tax: 0,
		tax_applied: false,
		notes: null,
		items: [],
	});

	const form = reactive(createInitialForm());

	const addItem = () => {
		form.items.push({
			id: crypto.randomUUID(),
			product_sku_id: null,
			quantity: 1,
			measure_unit_id: 1,
			price: 0,
			discount: 0,
			subtotal: 0,
		});
	};

	const removeItem = (id) => {
		form.items = form.items.filter((item) => item.id !== id);
	};

	const updateItem = (index, value) => {
		form.items[index] = value;
	};

	const selectedSkuIds = computed(() =>
		form.items.map((item) => item.product_sku_id).filter(Boolean)
	);

	const submit = () => {
		emit("save", form);
	};

	const reset = () => {
		Object.assign(form, createInitialForm());
	};

	const errorFields = computed(() => {
		const errs = props.errors?.errors || {};
		return Object.keys(errs).reduce((acc, field) => {
			acc[field] = errs[field]?.[0];
			return acc;
		}, {});
	});

	watch(
		form,
		(newForm) => {
			if (newForm.sales_channel_id) errorFields.value.sales_channel_id = null;
			if (newForm.branch_id) errorFields.value.branch_id = null;
			if (newForm.items?.length) errorFields.value.items = null;
		},
		{ deep: true }
	);

	defineExpose({
		reset,
		submit,
	});

	const handleCustomerCreated = (customer) => {
		newCustomer.value.push({
			title: customer.phone,
			subtitle: customer.name,
			value: customer.id,
		});

		form.customer_id = customer.id;
	};
</script>

<template>
	<form @submit.prevent="submit">
		<div class="row g-4">
			<div class="col-12">
				<BaseCard :hover="false">
					<template #header>
						<p class="m-0 fw-semibold">
							{{ $t("sales.detail") }}
						</p>
					</template>
					<div class="col-12 mb-2">
						<BaseCheckbox v-model="form.tax_applied" :label="$t('sales.tax')" />
					</div>
					<div class="row g-3">
						<div class="col-lg-5 col-xl-4">
							<BaseInput
								id="ordered_at"
								v-model="form.ordered_at"
								type="datetime-local"
								:label="$t('date.ordered')"
							/>
						</div>
						<div class="col-xl-8 col-lg-7">
							<BaseLookupInput
								v-model="form.customer_id"
								:items="customers"
								:label="$t('customer.no')"
								autocomplete="off"
								placeholder="6281806060979"
								:action-label="
									$t('common.addNew', { item: $t('customer.title') })
								"
								@action="
									openModal(
										CustomerForm,
										{
											onCreated: handleCustomerCreated,
										},
										{ title: $t('common.addNew'), footer: true }
									)
								"
							/>
						</div>
						<div class="col-lg-6">
							<BaseSelect
								v-model="form.sales_channel_id"
								:label="$t('sales.channel')"
								:options="lookup.sales_channels"
								:placeholder="$t('select.one')"
								:error="errorFields.sales_channel_id"
								style="min-width: 240px"
							/>
						</div>
						<div class="col-lg-6">
							<BaseSelect
								v-model="form.branch_id"
								:label="$t('purchases.branch')"
								:options="lookup.branches"
								:placeholder="$t('select.one')"
								:error="errorFields.branch_id"
								style="min-width: 240px"
							/>
						</div>
						<div class="col-12">
							<BaseTextarea
								v-model="form.notes"
								:label="$t('purchases.notes')"
								:placeholder="$t('common.optional')"
							/>
						</div>
					</div>
				</BaseCard>
			</div>

			<div class="col-12">
				<BaseCard :hover="false">
					<template #header>
						<div
							class="d-flex justify-content-between align-items-center flex-wrap gap-1"
						>
							<p class="m-0 fw-semibold">
								{{ $t("sales.items") }}
							</p>
							<button
								type="button"
								class="btn btn-sm btn-primary"
								@click="addItem"
							>
								<i class="bi bi-plus-square me-2"></i>
								{{ $t("common.addNew") }}
							</button>
						</div>
					</template>

					<div
						class="table-responsive border rounded"
						:class="{ 'is-invalid': errorFields.items }"
					>
						<table class="table table-hover mb-0">
							<thead v-if="form.items.length > 0">
								<tr>
									<th scope="col" style="min-width: 64px" class="text-center">
										#
									</th>
									<th scope="col" style="min-width: 210px">
										{{ $t("tables.sku") }}
									</th>
									<th scope="col" style="min-width: 100px">Qty</th>
									<th scope="col" style="min-width: 150px">
										{{ $t("tables.unit") }}
									</th>
									<th scope="col" style="min-width: 200px">
										{{ $t("tables.price") }}
									</th>
									<th scope="col" style="min-width: 200px">
										{{ $t("tables.discount") }}
									</th>
									<th scope="col" style="min-width: 200px">
										{{ $t("tables.subtotal") }}
									</th>
								</tr>
							</thead>
							<tbody>
								<CreateItemRow
									v-for="(item, index) in form.items"
									:key="item.id"
									:item="item"
									:lookup="lookup"
									:selected-branch-id="form.branch_id"
									:excluded-ids="
										selectedSkuIds.filter((id) => id !== item.product_sku_id)
									"
									@update:item="updateItem(index, $event)"
									@remove="removeItem(item.id)"
								/>
								<tr
									v-if="form.items.length === 0"
									class="cursor-pointer border rounded"
									@click="addItem"
								>
									<td
										colspan="7"
										class="text-center text-muted py-4"
										:class="{ 'is-invalid': errorFields.items }"
									>
										<i class="bi bi-cart-plus d-block display-3 mb-2"></i>
										{{ $t("tables.clickToAddItem") }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div v-if="errorFields.items" class="invalid-feedback">
						{{ errorFields.items }}
					</div>
				</BaseCard>
			</div>
		</div>
	</form>
</template>

<style scoped>
	.border.is-invalid,
	td.is-invalid {
		border-color: var(--bs-form-invalid-border-color) !important;
	}
</style>
