<script setup>
	import { computed, reactive, watch } from "vue";
	import { buildPayload, generateRandomCode } from "@/resources/products.js";
	import CreateBasicInfoCard from "./CreateBasicInfoCard.vue";
	import CreateAttributesCard from "./CreateAttributesCard.vue";
	import VariantsTableCard from "./VariantsTableCard.vue";

	const props = defineProps({
		lookup: {
			type: Object,
			default: () => ({}),
		},
		product: {
			type: Object,
			default: null,
		},
		onSave: {
			type: Function,
			default: () => {},
		},
	});

	const emit = defineEmits(["save", "close"]);

	const form = reactive({
		code: generateRandomCode(6),
		name: "",
		price: "",
		description: null,
		categories: [],
		selectedAttributes: [],
		hasVariants: false,
		skus: [],
	});

	const avgSellingPrice = computed(() => {
		const prices = form.skus
			.map((sku) => Number(sku.selling_price))
			.filter((price) => price > 0);

		if (!prices.length) {
			return 0;
		}

		return prices.reduce((sum, price) => sum + price, 0) / prices.length;
	});

	watch(
		form,
		(newForm) => {
			if (newForm.name?.trim()) errors.name = "";
			if (newForm.price !== 0) errors.price = "";
			if (newForm.categories?.length) errors.categories = "";
		},
		{ deep: true }
	);

	const getSelectedAttributes = (skus) => {
		const selected = new Map();
		skus.forEach((sku) => {
			sku.variants?.forEach((variant) => {
				const attributeId = variant.product_attribute_id;

				if (!attributeId) {
					return;
				}

				if (!selected.has(attributeId)) {
					const attribute = props.lookup.product_attributes?.find(
						(item) => item.value === attributeId
					);
					if (!attribute) {
						return;
					}
					selected.set(attributeId, {
						attribute: { label: attribute.title, value: attribute.value },
						valueIds: [],
					});
				}

				const item = selected.get(attributeId);

				if (!item.valueIds.includes(variant.id)) {
					item.valueIds.push(variant.id);
				}
			});
		});

		return [...selected.values()];
	};

	const errors = reactive({
		name: "",
		price: "",
		categories: "",
		skus: {},
	});

	const validate = () => {
		errors.name = "";
		errors.price = "";
		errors.categories = "";

		let valid = true;

		if (!form.name.trim()) {
			errors.name = "errors.products.name";
			valid = false;
		}

		if (form.price == "0" || !form.price) {
			errors.price = "errors.products.price";
			valid = false;
		}

		if (!form.categories.length) {
			errors.categories = "errors.products.categories";
			valid = false;
		}

		return valid;
	};

	const reset = () => {
		Object.assign(form, {
			code: generateRandomCode(6),
			name: "",
			price: "",
			description: null,
			categories: [],
			selectedAttributes: [],
			hasVariants: false,
			skus: [],
		});

		errors.name = "";
		errors.price = "";
		errors.categories = "";
		errors.skus = {};
	};

	const submit = async () => {
		if (!validate()) {
			return;
		}

		form.price ||= avgSellingPrice.value;

		const data = {
			id: props.product?.id ?? null,
			slug: props.product?.slug ?? null,
			...buildPayload(form),
		};

		if (props.product) {
			props.onSave?.(data);
		} else {
			emit("save", data);
		}
	};

	defineExpose({
		reset,
		submit,
	});

	const fillForm = (product) => {
		const skus = (product.skus ?? []).map((sku) => {
			const values =
				sku.variants?.map((variant) => ({
					id: variant.id,
					title: variant.value,
					subtitle: variant.product_attribute?.name,
					value: variant.id,
					meta: {
						product_attribute_id: variant.product_attribute_id,
						abbr: variant.abbr,
					},
				})) ?? [];

			return {
				...sku,
				id: sku.id ?? null,
				key: values
					.map((value) => value.id)
					.sort((a, b) => a - b)
					.join("-"),
				barcode: sku.barcode ?? "",
				cost_price: sku.cost_price ?? 0,
				selling_price: sku.selling_price ?? product.price ?? 0,
				values,
			};
		});

		const hasVariants = skus.some((sku) => sku.values.length > 0);

		Object.assign(form, {
			code: product.code ?? generateRandomCode(6),
			name: product.name ?? "",
			price: product.price ?? "",
			description: product.description ?? null,
			categories:
				product.categories.map((category) => ({
					label: category.name,
					value: category.id,
				})) ?? [],
			hasVariants,
			selectedAttributes: getSelectedAttributes(skus),
			skus,
		});

		errors.name = "";
		errors.price = "";
		errors.categories = "";
		errors.skus = {};
	};

	watch(
		() => props.product,
		(product) => {
			if (product) {
				fillForm(product);
			} else {
				reset();
			}
		},
		{ immediate: true, deep: true }
	);
</script>

<template>
	<form @submit.prevent="submit">
		<div :class="['row', 'g-3']">
			<div :class="form.hasVariants ? 'col-lg-7' : 'col-12'">
				<CreateBasicInfoCard
					v-model:name="form.name"
					v-model:price="form.price"
					v-model:description="form.description"
					v-model:categories="form.categories"
					v-model:has-variants="form.hasVariants"
					:lookup="lookup"
					:errors="errors"
				/>
			</div>
			<div v-if="form.hasVariants" class="col-lg-5">
				<CreateAttributesCard
					v-model="form.selectedAttributes"
					:lookup="lookup"
				/>
			</div>
			<div v-if="form.hasVariants" class="col-12">
				<VariantsTableCard
					v-model="form.skus"
					:lookup="lookup"
					:selected-attributes="form.selectedAttributes"
					:code="form.code"
				/>
			</div>
		</div>
	</form>
</template>
