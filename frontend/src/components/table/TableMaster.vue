<script setup>
	import { computed, watch, provide, reactive } from "vue";
	import {
		useBreakpoints,
		breakpointsBootstrapV5,
		watchDebounced,
	} from "@vueuse/core";
	import { TABLE_KEY } from "./utils/variables.js";
	import normalizeColumns from "./utils/normalizeColumns.js";
	import Toolbar from "./partials/TableToolbar.vue";
	import Container from "./partials/TableContainer.vue";
	import Length from "./partials/TableLength.vue";
	import Info from "./partials/TableInfo.vue";
	import Pagination from "./partials/TablePagination.vue";

	const selected = defineModel("selected", {
		type: Array,
		default: () => [],
	});

	const props = defineProps({
		columns: {
			type: Array,
			required: true,
		},
		raw: {
			type: Object,
			required: true,
		},
		size: {
			type: String,
			default: "md",
		},
		selectable: {
			type: Boolean,
			default: false,
		},
		searchable: {
			type: Boolean,
			default: true,
		},
		sortable: {
			type: Boolean,
			default: false,
		},
		striped: {
			type: Boolean,
			default: false,
		},
		info: {
			type: Boolean,
			default: true,
		},
		hover: {
			type: Boolean,
			default: false,
		},
		bordered: {
			type: Boolean,
			default: false,
		},
		rounded: {
			type: Boolean,
			default: false,
		},
		paging: {
			type: Boolean,
			default: true,
		},
		hasPagination: {
			type: Boolean,
			default: true,
		},
		server: {
			type: Boolean,
			default: false,
		},
	});

	const emit = defineEmits(["request"]);

	const breakpoints = useBreakpoints(breakpointsBootstrapV5);
	const isMobile = breakpoints.smaller("md");

	const state = reactive({
		search: "",
		perPage: 12,
		page: 1,
		sortBy: null,
		sortDirection: "asc",
		selected: new Set(),
	});

	const normalizedColumns = computed(() => normalizeColumns(props.columns));
	const computedRows = computed(() => props.raw.data ?? []);
	const rawData = computed(() => props.raw ?? {});

	const pagination = computed(() => {
		const { links = {}, meta = {} } = rawData.value;
		return { links, meta };
	});

	const selectedRows = computed(() =>
		computedRows.value.filter((row) => state.selected.has(row.id))
	);

	const visibleRows = computed(() => computedRows.value);

	const selectedVisibleRows = computed(() =>
		visibleRows.value.filter((row) => state.selected.has(row.id))
	);

	const selectedCount = computed(() => state.selected.size);
	const hasSelection = computed(() => selectedCount.value > 0);
	const allVisibleSelected = computed(
		() =>
			visibleRows.value.length > 0 &&
			selectedVisibleRows.value.length === visibleRows.value.length
	);

	const partiallySelected = computed(
		() => selectedVisibleRows.value.length > 0 && !allVisibleSelected.value
	);

	function setPage(page) {
		state.page = page;
	}

	function setPerPage(perPage) {
		state.perPage = perPage;
		state.page = 1;
	}

	function setSearch(keyword) {
		state.search = keyword;
		state.page = 1;
	}

	function setSort(column) {
		if (state.sortBy !== column) {
			state.sortBy = column;
			state.sortDirection = "asc";
			state.page = 1;
			return;
		}

		if (state.sortDirection === "asc") {
			state.sortDirection = "desc";
			state.page = 1;
			return;
		}

		state.sortBy = null;
		state.sortDirection = "asc";
		state.page = 1;
	}

	function toggleSelection(id) {
		if (state.selected.has(id)) {
			state.selected.delete(id);
		} else {
			state.selected.add(id);
		}
	}

	function clearSelection() {
		state.selected.clear();
	}

	function requestData() {
		emit("request", {
			page: state.page,
			per_page: state.perPage,
			search: state.search,
			sort_by: state.sortBy,
			sort_direction: state.sortDirection,
		});
	}

	watch(
		() => [state.page, state.perPage, state.sortBy, state.sortDirection],
		requestData
	);

	watch(
		selectedRows,
		(rows) => {
			selected.value = rows;
		},
		{
			immediate: true,
		}
	);

	watch(
		() => selected.value?.length,
		(length) => {
			if (length === 0 && state.selected.size > 0) {
				clearSelection();
			}
		},
		{
			immediate: true,
		}
	);

	watchDebounced(() => state.search, requestData, {
		debounce: 300,
		maxWait: 1200,
	});

	function selectVisible() {
		for (const row of visibleRows.value) {
			state.selected.add(row.id);
		}
	}

	function clearVisible() {
		for (const row of visibleRows.value) {
			state.selected.delete(row.id);
		}
	}

	function toggleVisibleSelection() {
		if (allVisibleSelected.value) {
			clearVisible();
			return;
		}
		selectVisible();
	}

	const tableProvider = reactive({
		state,
		options: {
			selectable: props.selectable,
			searchable: props.searchable,
			sortable: props.sortable,
		},
		style: {
			bordered: props.bordered,
			hover: props.hover,
			rounded: props.rounded,
			striped: props.striped,
		},
		data: {
			rows: computedRows,
			columns: normalizedColumns,
			visibleRows,
			selectedVisibleRows,
		},
		actions: {
			setPage,
			setPerPage,
			setSearch,
			setSort,
			toggleSelection,
			toggleVisibleSelection,
			selectVisible,
			clearVisible,
			clearSelection,
		},
		meta: {
			pagination,
			selectedCount,
			hasSelection,
			allVisibleSelected,
			partiallySelected,
			selectedRows,
			isMobile,
		},
	});

	provide(TABLE_KEY, tableProvider);
</script>

<template>
	<Info v-if="info" />
	<Toolbar>
		<template #toolbar-start>
			<slot name="toolbar-start" />
		</template>
		<template #toolbar-end>
			<slot name="toolbar-end" />
		</template>
	</Toolbar>
	<Container :size="size">
		<template v-for="(i, slotName) in $slots" #[slotName]="slotProps">
			<slot :name="slotName" v-bind="slotProps"></slot>
		</template>
	</Container>
	<div
		v-if="paging || hasPagination"
		class="d-flex justify-content-between gap-3 align-items-center flex-wrap mt-3"
	>
		<Length v-if="paging" />
		<Pagination v-if="hasPagination" />
	</div>
</template>
