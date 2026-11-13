<script setup>
	import { onMounted, ref, nextTick } from "vue";
	import { columns } from "@/resources/products.js";
	import { useToast } from "@/composables/toast.js";
	import { useModal } from "@/composables/modal.js";
	import { useAuthStore } from "@/stores/auth.js";
	import { useModalStore } from "@/stores/modal.js";
	import { useLookupStore } from "@/stores/lookup.js";
	import { useI18n } from "vue-i18n";

	import BaseButton from "@/components/base/BaseButton.vue";
	import Table from "@/components/table/TableMaster.vue";
	import FormModal from "@/components/modal/FormModal.vue";
	import ImageModal from "@/components/modal/ImageModal.vue";
	import CreateForm from "@/components/forms/products/CreateForm.vue";
	import api from "@/services/api.js";

	const auth = useAuthStore();
	const lookup = useLookupStore();
	const modal = useModalStore();
	const { openModal, closeModal } = useModal();
	const toast = useToast();
	const { t } = useI18n();

	const raw = ref({});
	const errors = ref([]);
	const selectedProducts = ref([]);
	const productForm = ref(null);
	const saving = ref(false);
	const show = ref(false);

	const fetchProducts = async (params = {}) => {
		try {
			const response = await api.get("/api/dashboard/products", params);
			raw.value = response;
		} catch (error) {
			if (error.code === "ERR_CANCELED" || error.name === "CanceledError")
				return;
			throw error;
		}
	};

	const handleRequest = async (params) => {
		await fetchProducts(params);
	};

	const onSave = async (payload) => {
		saving.value = true;
		errors.value = {};

		const { slug, ...data } = payload;

		try {
			let msgString = "";

			if (slug) {
				msgString = "updated";
				await api.put(`/api/dashboard/products/${slug}`, data);
			} else {
				msgString = "created";
				await api.post("/api/dashboard/products", data);
			}

			await fetchProducts();

			productForm.value?.reset();
			show.value = false;

			const escapedCode = `<strong>#${payload.code}</strong>`;

			toast.success(
				t("notifications.title"),
				t(`products.${msgString}`, { code: escapedCode })
			);

			lookup.load(["skus"], true);

			closeModal();
		} catch (error) {
			console.warn("Failed to save product:", error);

			errors.value = error.response?.data?.errors ?? {};

			if (!Object.keys(errors.value).length) {
				toast.error(
					t("notifications.title"),
					error.response?.data?.message ?? "Something went wrong."
				);
			}
		} finally {
			saving.value = false;
		}
	};

	const saveProduct = async () => {
		await productForm.value?.submit();
	};

	const resetForm = async () => {
		productForm.value?.reset();
	};

	const editProduct = async (product) => {
		try {
			const response = await api.get(`/api/dashboard/products/${product.slug}`);
			openModal(
				CreateForm,
				{ lookup, product: response.data, onSave },
				{ title: "products.edit", footer: true, size: "xl" }
			);
		} catch (error) {
			console.error(error);
		}
	};

	const deleteProduct = async (product) => {
		const escapedCode = `<strong>${product.name}</strong>`;

		const confirmed = await modal.open({
			title: t("common.confirm"),
			message: t("products.delete", { code: escapedCode }),
			confirmText: t("common.yes"),
			cancelText: t("common.no"),
		});

		if (!confirmed) return;

		try {
			await api.delete(`/api/dashboard/products/${product.slug}`);
		} catch (error) {
			console.error(error);
		} finally {
			toast.success(
				t("notifications.title"),
				t("products.deleted", { code: escapedCode })
			);

			nextTick();
			fetchProducts();
		}
	};

	onMounted(() => {
		fetchProducts();
		lookup.load(
			["product_categories", "product_attribute_values", "product_attributes"],
			true
		);
	});

	const isModalOpen = ref(false);
	const modalImage = ref("");
	const modalAlt = ref("");

	const openImageModal = (url, alt = "Image") => {
		modalImage.value = url;
		modalAlt.value = alt;
		isModalOpen.value = true;
	};
</script>

<template>
	<Teleport v-if="auth.isAdmin" defer to="#btnAddOnNavbar">
		<BaseButton type="button" variant="outline-primary" @click="show = true">
			<i class="bi bi-plus-square me-2"></i>
			{{ $t("common.addNew", { item: $t("products.title") }) }}
		</BaseButton>
	</Teleport>
	<div class="col-12">
		<h2 class="m-0">{{ $t("products.title") }}</h2>
		<Table
			v-model:selected="selectedProducts"
			:columns="columns"
			:raw="raw"
			striped
			hover
			selectable
			@request="handleRequest"
		>
			<template #cell-categories="{ row }">
				<template
					v-for="(category, index) in row.categories"
					:key="category.id"
				>
					<RouterLink
						class="text-reset text-decoration-none"
						:to="{ name: 'category.show', params: { slug: category.slug } }"
					>
						{{ category.name }}
					</RouterLink>
					<span v-if="index < row.categories.length - 1">, </span>
				</template>
			</template>
			<template #cell-image_url="{ row }">
				<span
					class="cursor-pointer"
					@click="openImageModal(row.image_url, row.code)"
					>{{ $t("columns.image") }}</span
				>
			</template>
			<template #cell-slug="{ row }">
				<BaseButton
					variant="secondary"
					size="sm"
					@click.stop="editProduct(row)"
				>
					<i class="bi bi-pencil-square me-2"></i>
					{{ $t("common.edit") }}
				</BaseButton>
			</template>
			<template #toolbar-end>
				<BaseButton
					:disabled="selectedProducts.length !== 1"
					variant="danger"
					@click="deleteProduct(selectedProducts[0])"
				>
					{{ $t("common.delete") }}
					<i class="bi bi-trash ms-2"></i>
				</BaseButton>
			</template>
		</Table>
	</div>
	<ImageModal
		v-model="isModalOpen"
		:image-src="modalImage"
		:alt-text="modalAlt"
	/>
	<FormModal
		v-model="show"
		:title="$t('common.addNew', { item: $t('products.title') })"
		:loading="saving"
		size="xl"
	>
		<CreateForm ref="productForm" :lookup="lookup" @save="onSave" />
		<template #footer>
			<BaseButton
				type="button"
				variant="danger"
				:disabled="saving"
				@click="resetForm"
			>
				{{ $t("common.reset") }}
				<i class="bi bi-arrow-repeat ms-2"></i>
			</BaseButton>

			<div class="d-flex gap-3">
				<BaseButton
					type="button"
					variant="secondary"
					:disabled="saving"
					@click="show = false"
				>
					{{ $t("common.cancel") }}
					<i class="bi bi-x-square ms-2"></i>
				</BaseButton>
				<BaseButton
					type="button"
					variant="primary"
					:loading="saving"
					:disabled="saving"
					@click="saveProduct"
				>
					{{ $t("common.save") }}
					<i class="bi bi-floppy ms-2"></i>
				</BaseButton>
			</div>
		</template>
	</FormModal>
</template>
