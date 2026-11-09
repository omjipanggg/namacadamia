<script setup>
	import { useAuthStore } from "@/stores/auth.js";
	import BaseCard from "@/components/base/BaseCard.vue";

	const authStore = useAuthStore();
</script>

<template>
	<div class="col-lg-5">
		<h2 class="mb-4">{{ $t("dashboard.profile") }}</h2>
		<BaseCard>
			<div class="table-responsive">
				<table class="table m-0 table-borderless">
					<tbody>
						<tr>
							<th scope="row">{{ $t("columns.name") }}</th>
							<td>{{ authStore.user.name }}</td>
						</tr>
						<tr>
							<th scope="row">{{ $t("columns.email") }}</th>
							<td>{{ authStore.user.email }}</td>
						</tr>
						<tr>
							<th scope="row">{{ $t("columns.roles") }}</th>
							<td>
								<span class="badge bg-primary">
									{{ authStore.user.roles.map((role) => role.name).join(", ") }}
								</span>
							</td>
						</tr>
						<tr>
							<th scope="row">{{ $t("columns.status") }}</th>
							<td>
								<span
									class="badge"
									:class="authStore.user.is_active ? 'bg-success' : 'bg-danger'"
								>
									{{
										authStore.user.is_active
											? $t("common.active").toLocaleLowerCase()
											: $t("common.inactive").toLocaleLowerCase()
									}}
								</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</BaseCard>
	</div>
</template>

<style scoped></style>
