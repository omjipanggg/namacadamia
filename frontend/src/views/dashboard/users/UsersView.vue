<script setup>
	import { onMounted, ref, reactive } from "vue";
	import api from "@/services/api.js";
	import { useLookupStore } from "@/stores/lookup.js";
	import { useNotificationStore } from "@/stores/notification.js";
	import { useToast } from "@/composables/toast.js";
	import { columns } from "@/resources/users.js";
	import Table from "@/components/table/TableMaster.vue";
	import BaseButton from "@/components/base/BaseButton.vue";

	import FormModal from "@/components/modal/FormModal.vue";
	import CForm from "@/components/forms/users/CrudForm.vue";

	const lookup = useLookupStore();
	const notify = useNotificationStore();
	const toast = useToast();

	const rows = ref({});
	const showAddModal = ref(false);
	const loading = ref(false);
	const saving = ref(false);

	const fetchUsers = async (params = {}) => {
		loading.value = true;
		try {
			const response = await api.get("/api/users", params);
			rows.value = response;
		} catch (error) {
			console.error(error);
		} finally {
			loading.value = false;
		}
	};

	const initialForm = () => ({
		name: "",
		email: "",
		password: "",
		is_active: false,
		roles: [],
		branches: [],
	});

	const form = reactive(initialForm());

	const errors = reactive({
		name: "",
		email: "",
		password: "",
		branches: "",
		roles: "",
	});

	onMounted(() => {
		fetchUsers();
		lookup.load(["branches", "roles"], true);
	});

	const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	const clearErrors = () => {
		for (const key in errors) {
			errors[key] = "";
		}
	};

	const selectedUsers = ref([]);

	const validateForm = () => {
		clearErrors();

		errors.name = !form.name.trim() ? "errors.products.name" : "";
		errors.email = !emailRegex.test(form.email) ? "errors.invalidEmail" : "";
		errors.password = !form.password.trim() ? "errors.columns.password" : "";

		return !Object.values(errors).some((value) => value !== "");
	};

	const submit = async () => {
		if (!validateForm()) return;
		saving.value = true;

		const payload = {
			...form,
			branches: form.branches.map((branch) => branch.value ?? branch),
			roles: form.roles.map((role) => role.value ?? role),
		};

		try {
			await api.post("/api/users", payload);
			await fetchUsers();
		} catch (error) {
			console.error(error);
		} finally {
			saving.value = false;
			showAddModal.value = false;

			Object.assign(form, initialForm());
		}
	};

	const blastNow = async () => {
		const ids = selectedUsers.value?.map((user) => user.id);

		try {
			await notify.send({ ids });
		} catch (error) {
			console.error("Cannot blast nothing: ", error);
		} finally {
			toast.success("notifications.title", "notifications.sent");
		}
	};

	const resetPassword = async () => {
		const ids = selectedUsers.value?.map((user) => user.id);

		try {
			await api.put("/api/users/bulk-reset", { ids });
		} catch (error) {
			console.error("Failed to reset passwrod: ", error);
		} finally {
			toast.success("notifications.title", "auth.password.resetted");
		}
	};

	const toggleActive = async () => {
		const ids = selectedUsers.value?.map((user) => user.id);

		try {
			await api.put("/api/users/bulk-toggle-active", { ids });
		} catch (error) {
			console.log(error);
		} finally {
			await fetchUsers();
			toast.success("notifications.title", "users.statusChanged");
		}
	};
</script>

<template>
	<Teleport defer to="#btnAddOnNavbar">
		<BaseButton
			variant="primary"
			:disabled="loading"
			@click.stop="showAddModal = true"
		>
			<i class="bi bi-plus-square me-2"></i>
			{{ $t("common.addNew", { item: $t("users.single") }) }}
		</BaseButton>
	</Teleport>

	<div class="col-12">
		<h2 class="mb-0">{{ $t("users.title") }}</h2>
		<Table
			v-model:selected="selectedUsers"
			:columns="columns"
			:raw="rows"
			selectable
			striped
			hover
			@request="fetchUsers"
		>
			<template #toolbar-end>
				<div class="btn-group">
					<button
						type="button"
						class="btn btn-primary dropdown-toggle"
						data-bs-toggle="dropdown"
						aria-expanded="false"
						:disabled="selectedUsers.length === 0"
					>
						{{ $t("common.actions") }}
						<span v-if="selectedUsers.length > 0">
							({{ selectedUsers.length }})
						</span>
					</button>
					<ul class="dropdown-menu dropdown-menu-end">
						<li>
							<a
								class="dropdown-item"
								href="#"
								data-bs-dismiss="dropdown"
								@click="blastNow"
							>
								<i class="bi bi-bell me-2"></i>
								{{ $t("notifications.blast") }}
							</a>
						</li>
						<li>
							<a
								class="dropdown-item"
								href="#"
								data-bs-dismiss="dropdown"
								@click="resetPassword"
							>
								<i class="bi bi-braces-asterisk me-2"></i>
								{{ $t("auth.password.reset") }}</a
							>
						</li>
						<li><hr class="dropdown-divider" /></li>
						<li>
							<a
								class="dropdown-item text-danger"
								href="#"
								data-bs-dismiss="dropdown"
								@click="toggleActive"
							>
								<i class="bi bi-ban me-2"></i>
								{{ $t("common.deactive") }}
							</a>
						</li>
					</ul>
				</div>
			</template>
			<template #cell-image_url="{ row }">
				<a :href="row.image_url" target="_blank">{{ $t("columns.image") }}</a>
			</template>
			<template #cell-is_active="{ row }">
				<span
					:class="['badge', `text-bg-${row.is_active ? 'primary' : 'danger'}`]"
				>
					{{
						row.is_active
							? $t("common.active").toLowerCase()
							: $t("common.inactive").toLowerCase()
					}}
				</span>
			</template>
		</Table>
	</div>

	<FormModal
		v-model="showAddModal"
		:title="$t('common.addNew', { item: $t('users.single') })"
		:loading="loading"
		:overflow-visible="true"
		:center-footer="true"
	>
		<CForm
			v-model="form"
			:lookup="lookup"
			:loading="loading"
			:errors="errors"
		/>

		<template #footer>
			<BaseButton
				:loading="saving"
				:disabled="loading || saving"
				@click.stop="submit"
			>
				{{ $t("common.save") }}
				<i class="bi bi-floppy ms-2"></i>
			</BaseButton>
		</template>
	</FormModal>
</template>
