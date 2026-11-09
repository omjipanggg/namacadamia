<script setup>
	import { onMounted, ref, reactive } from "vue";
	import { storeToRefs } from "pinia";

	import BaseBadge from "@/components/base/BaseBadge.vue";
	import BaseButton from "@/components/base/BaseButton.vue";
	import BaseCard from "@/components/base/BaseCard.vue";
	import BaseTable from "@/components/table/TableMaster.vue";

	import FormModal from "@/components/modal/FormModal.vue";
	import Form from "@/components/forms/products/categories/CreateEditForm.vue";

	import { useProductStore } from "@/stores/products.js";

	const productStore = useProductStore();

	const { productCategories, loading } = storeToRefs(productStore);

	const openModal = ref(false);
	const dynamicForm = ref(null);

	const fetch = async (params) => {
		await productStore.fetchCategories(params);
	};

	onMounted(() => {
		fetch();
	});

	const columns = [
		{
			key: "name",
			label: "Name",
			sortable: true,
		},
		{
			key: "is_active",
			label: "Status",
			width: 120,
		},
		{
			key: "slug",
			label: "Action",
			width: 120,
		},
	];

	const initialForm = () => ({
		id: null,
		name: "",
		is_active: false,
	});

	const form = reactive(initialForm());

	const create = () => {
		Object.assign(form, initialForm());
		openModal.value = true;
	};

	const edit = (category) => {
		form.id = category.slug;
		form.name = category.name;
		form.is_active = !!category.is_active;
		openModal.value = true;
	};

	const emitSubmit = async (slug) => {
		try {
			if (slug) {
				await productStore.updateCategory(slug, form);
			} else {
				await productStore.createCategory(form);
			}
		} catch (error) {
			console.error(error);
		} finally {
			fetch();
			openModal.value = false;
		}
	};

	const destroy = async (slug) => {
		const confirmed = confirm("Are you sure?");

		if (!confirmed) return;

		try {
			await productStore.deleteCategory(slug);
		} catch (error) {
			console.error(error);
		} finally {
			fetch();
			alert("Consider it done");
		}
	};
</script>

<template>
	<BaseCard :hover="false">
		<template #header>
			<p class="fw-semibold m-0">
				{{ $t("products.categories") }}
			</p>
		</template>

		<BaseTable
			:columns="columns"
			:raw="productCategories"
			:info="false"
			striped
			bordered
			hover
			@request="fetch"
		>
			<template #toolbar-end>
				<BaseButton :disabled="loading" @click.stop="create()">
					<i class="bi bi-plus-square me-2"></i>
					{{ $t("common.addNew") }}
				</BaseButton>
			</template>
			<template #cell-name="{ row }">
				<input
					:id="row.id"
					type="text"
					class="form-control-plaintext"
					:placeholder="$t('products.inputName')"
					:value="row.name"
				/>
			</template>
			<template #cell-is_active="{ row }">
				<BaseBadge :variant="row.is_active ? 'success' : 'danger'">
					{{
						row.is_active
							? $t("common.active").toLowerCase()
							: $t("common.inactive").toLowerCase()
					}}</BaseBadge
				>
			</template>
			<template #cell-slug="{ row }">
				<div class="d-flex flex-wrap gap-2">
					<BaseButton size="sm" variant="secondary" @click.stop="edit(row)">
						<i class="bi bi-pencil-square"></i>
					</BaseButton>
					<BaseButton
						size="sm"
						variant="danger"
						@click.stop="destroy(row.slug)"
					>
						<i class="bi bi-trash"></i>
					</BaseButton>
				</div>
			</template>
		</BaseTable>
	</BaseCard>
	<FormModal
		v-model="openModal"
		:title="$t('columns.categories')"
		:loading="loading"
		:center-footer="true"
		size="md"
	>
		<Form ref="dynamicForm" v-model="form" @submit="emitSubmit" />
		<template #footer>
			<BaseButton :loading="loading" @click.stop="dynamicForm.submit()">
				{{ $t("common.save") }}
			</BaseButton>
		</template>
	</FormModal>
</template>
