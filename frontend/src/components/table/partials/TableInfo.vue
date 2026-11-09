<script setup>
import { useTable } from "../functions/useTable";
const tableState = useTable();
</script>

<template>
	<small class="text-body-secondary fw-semibold d-block mb-4">
		{{
			`${$t("pagination.showing")} ${tableState.meta.pagination.meta.from ?? 0} ${$t("pagination.to")} ${tableState.meta.pagination.meta.to ?? 0} ${$t("pagination.of")} ${tableState.meta.pagination.meta.total ?? 0} ${$t("pagination.entries")}`
		}}.
	</small>
</template>
