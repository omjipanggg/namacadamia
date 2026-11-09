<script setup>
	import { computed } from "vue";
	import { useTable } from "../functions/useTable";
	const tableState = useTable();

	const pagination = computed(() => tableState.meta.pagination);

	function changePage(link) {
		if (link.active || !link.page) return;
		tableState.actions.setPage(link.page);
	}

	const prevLink = computed(() => {
		return pagination.value.links.prev;
	});

	const nextLink = computed(() => {
		return pagination.value.links.next;
	});

	const displayPages = computed(() => {
		const meta = pagination?.value.meta || {};
		const totalPages = meta.last_page || 1;
		const currentPage = meta.current_page || 1;

		return Array.from({ length: totalPages }, (_, index) => ({
			page: index + 1,
			active: index + 1 === currentPage,
		}));
	});
</script>

<template>
	<nav>
		<ul class="pagination page-box mb-0">
			<template v-if="tableState.meta.isMobile">
				<li :class="['page-item', { disabled: !prevLink }]">
					<button
						class="page-link"
						:disabled="!prevLink"
						@click="changePage(prevLink)"
					>
						<i class="bi bi-chevron-left"></i>
					</button>
				</li>
				<li class="page-item disabled">
					<span class="page-link fw-semibold">
						{{ pagination.meta.current_page }}
						/
						{{ pagination.meta.last_page }}
					</span>
				</li>
				<li :class="['page-item', { disabled: !nextLink }]">
					<button
						class="page-link"
						:disabled="!nextLink"
						@click="changePage(nextLink)"
					>
						<i class="bi bi-chevron-right"></i>
					</button>
				</li>
			</template>
			<template v-else>
				<li
					:class="[
						'page-item',
						{ disabled: pagination.meta.current_page === 1 },
					]"
				>
					<button
						type="button"
						class="page-link"
						:disabled="pagination.meta.current_page === 1"
						@click="changePage(pagination.meta.current_page - 1)"
					>
						<i class="bi bi-chevron-left"></i>
					</button>
				</li>

				<li
					v-for="item in displayPages"
					:key="item.page"
					:class="['page-item', { active: item.active }]"
				>
					<button
						type="button"
						class="page-link"
						:disabled="item.active"
						@click="changePage(item.page)"
					>
						{{ item.page }}
					</button>
				</li>

				<li
					:class="[
						'page-item',
						{
							disabled:
								pagination.meta.current_page === pagination.meta.last_page,
						},
					]"
				>
					<button
						type="button"
						class="page-link"
						:disabled="
							pagination.meta.current_page === pagination.meta.last_page
						"
						@click="changePage(pagination.meta.current_page + 1)"
					>
						<i class="bi bi-chevron-right"></i>
					</button>
				</li>
			</template>
		</ul>
	</nav>
</template>
