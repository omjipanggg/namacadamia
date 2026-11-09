<script setup>
	import { computed, ref, watch } from "vue";
	import BaseAlert from "@/components/base/BaseAlert.vue";
	import BaseCheckbox from "@/components/base/BaseCheckbox.vue";
	import BaseInput from "@/components/base/BaseInput.vue";
	// import BaseSelect from "@/components/base/BaseSelect.vue";
	import BaseTextarea from "@/components/base/BaseTextarea.vue";

	const supplierId = defineModel("supplierId", {
		type: [String, Number],
		default: 1,
	});

	const orderedAt = defineModel("orderedAt", {
		type: [String, Date],
		default: null,
	});

	const stockLocationId = defineModel("stockLocationId", {
		type: [String, Number],
		default: 1,
	});

	const autoReceive = defineModel("autoReceive", {
		type: Boolean,
		default: true,
	});

	const notes = defineModel("notes", {
		type: String,
		default: "",
	});

	const props = defineProps({
		errors: {
			type: Object,
			default: () => ({}),
		},
		lookup: {
			type: Object,
			default: () => ({
				branches: [],
				suppliers: [],
				units: [],
				skus: [],
			}),
		},
	});

	const showAlert = ref(false);
	// const showSupplierModal = ref(false);
	// const showBranchModal = ref(false);

	const allErrors = computed(() => {
		const errorObj = props.errors?.errors;
		if (!errorObj) return [];

		return Object.values(errorObj).flat();
	});

	watch(
		allErrors,
		(newErrors) => {
			showAlert.value = newErrors.length > 0;
		},
		{ immediate: true }
	);
</script>

<template>
	<BaseAlert
		v-if="allErrors.length"
		:key="JSON.stringify(allErrors)"
		v-model="showAlert"
		variant="danger"
		:dismissible="true"
	>
		<div class="fw-bold mb-1">{{ $t("errors.pleaseFix") }}</div>
		<ul class="mb-0 ps-3">
			<li v-for="(messages, key) in allErrors" :key="key">
				{{ messages }}
			</li>
		</ul>
	</BaseAlert>
	<div class="card">
		<div class="card-header">
			<p class="m-0 fw-semibold">
				{{ $t("purchases.detail") }}
			</p>
		</div>
		<div class="card-body">
			<div class="row g-3">
				<div class="col-12">
					<BaseCheckbox
						v-model="autoReceive"
						:label="$t('purchases.autoReceive')"
					></BaseCheckbox>
				</div>
				<div class="col-lg-5 col-xl-4 mt-2">
					<BaseInput
						id="ordered_at"
						v-model="orderedAt"
						type="datetime-local"
						:label="$t('date.purchased')"
					/>
					<BaseInput v-model="stockLocationId" type="hidden" />
					<BaseInput v-model="supplierId" type="hidden" />
				</div>
				<!--
				<div class="w-100 p-0 m-0"></div>
                    <div class="col-md-6">
                        <BaseSelect
						id="supplier_id"
						v-model="supplierId"
						:label="$t('purchases.supplier')"
						:options="lookup.suppliers"
						:placeholder="
							$t('select.option', { item: `[${$t('purchases.supplier')}]` })
						"
						:action-label="
							$t('common.addNew', { item: $t('purchases.supplier') })
						"
						@action="showSupplierModal = true"
					/>
				</div>
				<div class="col-md-6">
					<BaseSelect
						id="branch_id"
						v-model="branchId"
						:label="$t('purchases.branch')"
						:options="lookup.branches"
						:placeholder="
							$t('select.option', { item: `[${$t('purchases.branch')}]` })
						"
						:action-label="
							$t('common.addNew', { item: $t('purchases.branch') })
						"
						@action="showBranchModal = true"
					/>
				</div>
                -->
				<div class="col-12">
					<BaseTextarea
						id="notes"
						v-model="notes"
						class="form-control"
						rows="3"
						:placeholder="$t('common.optional')"
						:label="$t('purchases.notes')"
					/>
				</div>
			</div>
		</div>
	</div>
</template>
