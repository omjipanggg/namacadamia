<script setup>
	import { ref, onMounted } from "vue";
	import { useLookupStore } from "@/stores/lookup.js";
	import { useRoute } from "vue-router";
	import { useToast } from "@/composables/toast.js";
	import { useI18n } from "vue-i18n";
	import BaseButton from "@/components/base/BaseButton.vue";
	import FormModal from "@/components/modal/FormModal.vue";
	import CreateForm from "@/components/forms/tables/CreateForm.vue";
	import Table from "@/components/table/TableMaster.vue";
	import api from "@/services/api.js";

	const { t } = useI18n();
	const toast = useToast();

	const showInsertModal = ref(false);
	const saving = ref(false);

	const data = ref([]);
	const columns = ref([]);
	const selectedRow = ref([]);
	const theForm = ref(null);
	const errors = ref({});

	const lookup = useLookupStore();
	const route = useRoute();

	const fetchData = async (params = {}) => {
		const response = await api.get(`/api/tables/${route.params.slug}`, params);
		data.value = response;
		columns.value = response.columns;
	};

	const saveData = async () => {
		await theForm.value.submit();
	};

	const handleRequest = async (params) => {
		await fetchData(params);
	};

	const handleDelete = async (data) => {
		saving.value = true;

		const confirmed = confirm("You sure?");

		if (!confirmed) return;

		try {
			const response = await api.delete(`/api/tables/${route.params.slug}`, {
				data,
			});
			toast.success(
				t("notifications.title"),
				t("tables.deleted", { count: response?.deleted_items_count })
			);
		} catch (error) {
			toast.error(t("common.failure"), error.message);
		} finally {
			fetchData();
			saving.value = false;
		}
	};

	const onSave = async (form) => {
		saving.value = true;
		try {
			await api.post(`/api/tables/${route.params.slug}`, form);
			toast.success(
				t("notifications.title"),
				t("tables.created", { table: route.params.slug })
			);
		} catch (error) {
			console.error(error);
		} finally {
			fetchData();
			showInsertModal.value = false;
			saving.value = false;
		}
	};

	onMounted(() => {
		fetchData();
		lookup.load(["users"], true);
	});
</script>
<template>
	<Teleport defer to="#btnAddOnNavbar">
		<BaseButton
			type="button"
			variant="outline-primary"
			@click="showInsertModal = true"
		>
			<i class="bi bi-plus-square me-2"></i>
			{{ $t("common.addNew", { item: `[${route.params.slug}]` }) }}
		</BaseButton>
	</Teleport>
	<FormModal
		v-model="showInsertModal"
		:title="`[${route.params.slug}]`"
		:loading="saving"
		size="md"
	>
		<div class="d-flex gap-3 flex-wrap flex-column">
			<CreateForm
				ref="theForm"
				:lookup="lookup"
				:errors="errors"
				:columns="columns"
				@save="onSave"
			/>
		</div>

		<template #footer>
			<BaseButton
				type="button"
				variant="secondary"
				:disabled="saving"
				@click="showInsertModal = false"
			>
				{{ $t("common.cancel") }}
				<i class="bi bi-x-square ms-2"></i>
			</BaseButton>
			<BaseButton
				type="button"
				variant="primary"
				:loading="saving"
				@click="saveData"
			>
				{{ $t("common.save") }}
				<i class="bi bi-floppy ms-2"></i>
			</BaseButton>
		</template>
	</FormModal>
	<div class="col-12">
		<h2 class="mb-0">
			{{ route.params.slug }}
		</h2>
		<Table
			v-model:selected="selectedRow"
			:columns="columns"
			:raw="data"
			striped
			hover
			selectable
			@request="handleRequest"
		>
			<template #toolbar-end>
				<BaseButton
					:disabled="selectedRow.length === 0"
					variant="danger"
					@click="handleDelete(selectedRow)"
				>
					{{ $t("common.delete") }}
					<i class="bi bi-trash ms-2"></i>
				</BaseButton>
			</template>
		</Table>
	</div>
</template>
