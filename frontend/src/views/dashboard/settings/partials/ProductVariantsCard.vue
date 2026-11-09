<script setup>
	import { onMounted, reactive, ref, watch, useId } from "vue";
	import { useProductStore } from "@/stores/products.js";
	import { storeToRefs } from "pinia";

	import { useModalStore } from "@/stores/modal.js";
	import { useModal } from "@/composables/modal.js";
	import { useToast } from "@/composables/toast.js";

	import Preloader from "@/components/Preloader.vue";

	import BaseBadge from "@/components/base/BaseBadge.vue";
	import BaseButton from "@/components/base/BaseButton.vue";

	import AddProductVariantForm from "@/components/forms/parameters/AddProductVariantForm.vue";
	import UpdateProductVariantForm from "@/components/forms/parameters/UpdateProductVariantForm.vue";

	const productStore = useProductStore();

	const { productVariants, loading } = storeToRefs(productStore);

	const toast = useToast();
	const modalStore = useModalStore();
	const { openModal } = useModal();

	const selectedAttribute = ref({});

	const deleteSomething = async (attribute, isAttribute) => {
		let code = isAttribute ? attribute.name : attribute.value;

		const escapedCode = `<strong>${code}</strong>`;

		const confirmed = await modalStore.open({
			title: "common.confirm",
			message: { key: "products.delete", params: { code: escapedCode } },
			confirmText: "common.yes",
			cancelText: "common.no",
		});

		if (!confirmed) return;

		try {
			if (isAttribute) {
				await productStore.deleteAttributes(attribute.id);
			} else {
				await productStore.deleteValues(attribute.id);
			}
		} finally {
			productStore.fetchAttributes();
			toast.success("notifications.title", {
				key: "common.deleted",
				params: { item: escapedCode },
			});
		}
	};

	onMounted(() => {
		productStore.fetchAttributes();
	});

	const baseAttributeForm = () => ({
		id: null,
		name: "",
		description: "",
		is_active: 0,
		values: [],
	});

	const form = reactive(baseAttributeForm());

	const addValuesRow = (attributeId) => {
		if (!Array.isArray(form.values)) {
			form.values = [];
		}

		form.values.push({
			id: crypto.randomUUID(),
			product_attribute_id: attributeId || form.id,
			value: "",
			abbr: "",
		});
	};

	const removeValuesRow = (id) => {
		const index = form.values.findIndex((item) => item.id === id);
		if (index !== -1) {
			form.values.splice(index, 1);
		}
	};

	const updateValuesRow = async (id) => {
		try {
			await productStore.updateBulkAttributes(id, form);
			await productStore.fetchAttributes();
			toast.success("notifications.title", "common.updated");
		} catch (error) {
			console.error(error);
		}
	};

	watch(
		selectedAttribute,
		(newAttr) => {
			if (!newAttr) return;

			form.id = newAttr.id ?? null;
			form.name = newAttr.name ?? "";
			form.description = newAttr.description ?? "";
			form.is_active = newAttr.is_active ?? 1;
			form.values = Array.isArray(newAttr.values)
				? newAttr.values.map((value) => ({ ...value }))
				: [];
		},
		{ deep: true }
	);
</script>

<template>
	<div class="card">
		<div class="card-header">
			<p class="fw-semibold m-0">
				{{ $t("products.variants") }}
			</p>
		</div>
		<div class="card-body p-0">
			<div id="accordionAttributes" class="accordion accordion-flush">
				<template v-if="loading">
					<div class="p-3">
						<Preloader variant="spin" />
						<span class="ms-2">{{ $t("common.loading") }}</span>
					</div>
				</template>
				<template v-else>
					<div
						v-for="attribute in productVariants"
						:key="attribute.id"
						class="accordion-item"
					>
						<h2 class="accordion-header">
							<button
								class="accordion-button collapsed"
								type="button"
								data-bs-toggle="collapse"
								:data-bs-target="`#flush-${attribute.id}`"
								aria-expanded="false"
								aria-controls="flush-collapseOne"
								@click="selectedAttribute = attribute"
							>
								<div
									class="d-flex w-100 align-items-center justify-content-between gap-3"
									:class="{ 'text-secondary': !attribute.is_active }"
								>
									{{ attribute.name }}
									<BaseBadge
										v-if="!attribute.is_active"
										class="rounded-pill me-3"
										variant="danger"
									>
										<i class="bi bi-info-circle me-2"></i>
										{{ $t("common.inactive") }}
									</BaseBadge>
								</div>
							</button>
						</h2>
						<div
							:id="`flush-${attribute.id}`"
							class="accordion-collapse collapse"
							data-bs-parent="#accordionAttributes"
						>
							<div class="accordion-body">
								<div class="table-responsive">
									<table
										class="table table-hover table-bordered table-striped align-middle"
									>
										<thead>
											<tr>
												<th>{{ $t("common.code") }}</th>
												<th>{{ $t("common.name") }}</th>
												<th>{{ $t("common.actions") }}</th>
											</tr>
										</thead>
										<tbody>
											<template v-if="form.values.length > 0">
												<tr v-for="value in form.values" :key="value.id">
													<td class="text-monospace" style="width: 100px">
														<input
															:id="`abbr-${value.id}-${useId()}`"
															v-model="value.abbr"
															type="text"
															class="form-control-plaintext"
															maxlength="3"
														/>
													</td>
													<td>
														<input
															:id="`value-${value.id}-${useId()}`"
															v-model="value.value"
															type="text"
															class="form-control-plaintext"
														/>
													</td>
													<td style="width: 100px">
														<BaseButton
															variant="danger"
															size="sm"
															@click="removeValuesRow(value.id)"
														>
															<i class="bi bi-trash"></i>
														</BaseButton>
													</td>
												</tr>
											</template>
											<tr>
												<td colspan="2" class="fw-bold">
													{{ $t("tables.clickToAddItem") }}
												</td>
												<td style="width: 100px">
													<BaseButton
														variant="primary"
														size="sm"
														@click="addValuesRow(attribute.id)"
													>
														<i class="bi bi-plus-square"></i>
													</BaseButton>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div
									class="d-flex gap-3 align-items-center justify-content-between flex-wrap"
								>
									<div class="gap-3 d-flex">
										<BaseButton
											:disabled="loading"
											variant="secondary"
											@click="
												openModal(
													UpdateProductVariantForm,
													{ loading: loading, data: attribute },
													{
														footer: true,
														title: $t('common.edit'),
													}
												)
											"
										>
											<i class="bi bi-pencil-square me-1"></i>
											{{ $t("common.edit") }}
										</BaseButton>
										<BaseButton
											variant="danger"
											@click="deleteSomething(attribute, true)"
										>
											<i class="bi bi-trash me-1"></i>
											{{ $t("common.delete") }}
										</BaseButton>
									</div>
									<BaseButton
										variant="success"
										:loading="loading"
										@click="updateValuesRow(attribute.id)"
									>
										<i class="bi bi-floppy me-1"></i>
										{{ $t("common.save") }}
									</BaseButton>
								</div>
							</div>
						</div>
					</div>
				</template>
			</div>
		</div>
		<div class="card-footer">
			<BaseButton
				variant="primary"
				:disabled="loading"
				size="sm"
				@click="
					openModal(
						AddProductVariantForm,
						{ loading },
						{
							title: $t('products.variants'),
							footer: true,
						}
					)
				"
			>
				<i class="bi bi-plus-square me-1"></i>
				{{ $t("common.addNew") }}
			</BaseButton>
		</div>
	</div>
</template>
