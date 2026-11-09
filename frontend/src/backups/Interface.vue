<script>
	const filteredRows = computed(() => {
		if (!state.search.trim()) {
			return;
			computedRows.value;
		}
		const keyword = state.search.toLowerCase();

		computedRows.value.filter((row) =>
			JSON.stringify(row).toLowerCase().includes(keyword)
		);
	});

	const getErrorMessage = (response) => {
		return response?.message;

		// if (response?.errors) return Object.values(response.errors).flat();
		// if (response?.message) return [response.message];
		// return ["An unexpected error occurred."];
	};

	watch(
		() => form.name,
		(value) => {
			if (value.trim()) {
				errors.name = "";
			}
		}
	);

	watch(
		() => form.price,
		(value) => {
			if (value !== 0) {
				errors.price = "";
			}
		}
	);

	watch(
		() => form.categories,
		(value) => {
			if (value.length) {
				errors.categories = "";
			}
		},
		{ deep: true }
	);
</script>

<!-- //StocksView.vue -->
 
	<div :class="isDetailOpen ? 'col-lg-4' : 'col-12'">
		<div class="row g-3">
			<div
				v-for="branch in branchStore.branches.data"
				:key="branch.id"
				:class="isDetailOpen ? 'col-xl-12 col-md-6 ' : 'col-xl-3 col-md-6'"
			>
				<BaseCard
					:hover="selectedBranch !== branch.code"
					:clickable="true"
					:class="{
						'active bg-secondary-subtle': selectedBranch === branch.code,
					}"
					@click="
						() => {
							selectedBranch = branch.code;
							branchStore.show(branch.id);
							isDetailOpen = true;
						}
					"
				>
					<template #header>
						<small
							class="text-muted small"
							:class="{
								'fw-semibold text-reset': selectedBranch === branch.code,
							}"
						>
							#{{ branch.code }}
						</small>
					</template>
					{{ branch.name }}
				</BaseCard>
			</div>
		</div>
	</div>


<BaseInput
	id="search"
	v-model="filters.search"
	class="form-control"
	:disabled="saleStore.loading"
	:placeholder="$t('search.title') + '...'"
	autocomplete="search"
></BaseInput>

// TablePagination.vue

				<li
					v-for="(pageLink, i) in tableState.meta.pagination.meta.links"
					:key="i"
					:class="[
						'page-item',
						{
							disabled: !pageLink.url,
							active: pageLink.active,
						},
					]"
					aria-label="page"
				>
					<button
						type="button"
						:class="[
							'page-link',
							{ disabled: pageLink.active || !pageLink.page },
						]"
						:disabled="pageLink.active || !pageLink.page"
						:aria-current="pageLink.active ? 'page' : 'no-page'"
						@click="changePage(pageLink)"
					>
						<template v-if="pageLink.label.includes('Previous')">
							<i class="bi bi-chevron-left"></i>
						</template>
						<template v-else-if="pageLink.label.includes('Next')">
							<i class="bi bi-chevron-right"></i>
						</template>
						<template v-else>
							{{ pageLink.label }}
						</template>
					</button>
				</li>
<template>
    // notifications
	<Teleport defer to="#btnAddOnNavbar">
		<BaseButton
			v-if="auth.isAdmin"
			type="button"
			variant="outline-secondary"
			:disabled="notifyStore.loading"
			:loading="notifyStore.loading"
			:class="[
				'position-relative',
				'ms-auto',
				{
					disabled: notifyStore.loading,
				},
			]"
			@click="handleTest"
		>
			{{ $t("notifications.test") }}
			<span
				class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
			>
				{{ notifyStore.unreadCount }}
				<span class="visually-hidden">{{ $t("notifications.unread") }}</span>
			</span>
		</BaseButton>
	</Teleport>
    </template>

<template v-if="props.loading">
	<tr v-for="i in 4" :key="i">
		<td v-for="column in props.columns" :key="column.key">
			<span class="placeholder-glow d-block">
				<span class="placeholder col-12"></span>
			</span>
		</td>
	</tr>
</template>
<template v-else>
	<table>
		<thead></thead>
		<tbody></tbody>
	</table>
</template>

<template>
	<div class="col-12">
		<Table
			:columns="columns"
			:raw="branches"
			noinfo
			striped
			hover
			@request="handleRequest"
		>
			<template #toolbar-end>
				<BaseButton
					variant="secondary"
					:disabled="loading"
					@click="handlePrint"
				>
					{{ $t("common.print") }}
					<i class="bi bi-printer ms-2"></i>
				</BaseButton>
			</template>
		</Table>
	</div>
</template>

<template>
	<Teleport v-if="auth.isAdmin" defer to="#btnAddOnNavbar">
		<div class="d-block d-md-none">
			<div class="dropdown">
				<BaseButton
					type="button"
					variant="outline-secondary"
					class="dropdown-toggle"
					data-bs-toggle="dropdown"
					aria-expanded="false"
				>
					<i class="bi bi-plus-square me-2"></i>
					{{ $t("common.addNew") }}
				</BaseButton>
				<ul class="dropdown-menu">
					<li>
						<a class="dropdown-item" href="#" @click="show = true">
							{{ $t("products.title") }}
						</a>
					</li>
					<li><a class="dropdown-item" href="#">SKU</a></li>
				</ul>
			</div>
		</div>
		<div class="d-md-block d-none">
			<div class="d-flex flex-wrap gap-3">
				<BaseButton
					type="button"
					variant="outline-primary"
					@click="show = true"
				>
					<i class="bi bi-plus-square me-2"></i>
					{{ $t("common.addNew", { item: $t("products.title") }) }}
				</BaseButton>
				<BaseButton
					type="button"
					variant="outline-primary"
					@click="showAddSkuModal = true"
				>
					<i class="bi bi-plus-square me-2"></i>
					{{ $t("common.addNew", { item: "SKU" }) }}
				</BaseButton>
			</div>
		</div>
	</Teleport>
</template>

<template #actions>
	<RouterLink
		:class="['text-decoration-none', 'text-reset']"
		:to="{
			name: 'dashboard.purchases.show',
			params: {
				id: row.id,
			},
		}"
	>
		{{ $t("common.view") }}
	</RouterLink>
	<a
		href="#"
		class="text-decoration-none text-reset"
		@click="receivePurchase(row.id)"
		>{{ $t("common.receive") }}</a
	>
	<BaseButton
		:disabled="selectedPurchases.length !== 1"
		@click="receivePurchase(selectedPurchases[0])"
	>
		Receive
	</BaseButton>

	<!-- // CreateItemRow.vue -->
	<select
		:id="`product-sku-${item.id}`"
		v-model="item.product_sku_id"
		required
		class="form-select"
	>
		<option :value="null" disabled>
			{{ $t("select.one") }}
		</option>
		<option v-for="sku in props.lookup.skus" :key="sku.id" :value="sku.id">
			[{{ sku.code }}] {{ sku.product_name }} ·
			{{ sku.attributes.join(" · ") }}
		</option>
	</select>
</template>

<!-- // AppSidebarItem -->
<script setup>
	import { useSidebarStore } from "@/stores/sidebar.js";
	import { useNotificationStore } from "@/stores/notification.js";
	import { useBreakpoints, breakpointsBootstrapV5 } from "@vueuse/core";

	const notifyStore = useNotificationStore();
	const sidebar = useSidebarStore();
	const breakpoints = useBreakpoints(breakpointsBootstrapV5);
	const isMobile = breakpoints.smaller("md");
	defineProps({
		item: {
			type: Object,
			default: () => ({}),
		},
	});
</script>

<template>
	<RouterLink
		:to="{ name: item.route }"
		class="sidebar-link position-relative"
		active-class="active"
		exact-active-class="exact-active"
		@click="isMobile && sidebar.toggle()"
	>
		<div class="group">
			<i :class="['bi', `bi-${item.icon}`, 'me-1']"></i>
			{{ $t(item.title) }}
		</div>

		<span
			v-if="notifyStore.unreadCount > 0 && item.key === 'notifications'"
			class="position-absolute badge bg-danger"
			style="right: 8px"
		>
			{{ notifyStore.unreadCount }}
			<span class="visually-hidden">{{ $t("notifications.unread") }}</span>
		</span>
	</RouterLink>
</template>

<style scoped></style>
