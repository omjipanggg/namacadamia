<script setup>
	import { onMounted } from "vue";
	import { useBranchStore } from "@/stores/branches.js";
	import { columns } from "@/resources/branches.js";
	import BaseButton from "@/components/base/BaseButton.vue";
	import Table from "@/components/table/TableMaster.vue";

	const branchStore = useBranchStore();

	onMounted(() => {
		branchStore.fetch();
	});

	const handlePrint = () => {
		alert("Printing...");
	};
</script>

<template>
	<div class="col-12">
		<h2>{{ $t("branches.title") }}</h2>
		<Table
			:columns="columns"
			:raw="branchStore.branches"
			striped
			hover
			@request="branchStore.fetch"
		>
			<template #toolbar-end>
				<BaseButton
					variant="secondary"
					:disabled="branchStore.loading"
					@click="handlePrint"
				>
					{{ $t("common.print") }}
					<i class="bi bi-printer ms-2"></i>
				</BaseButton>
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
</template>
