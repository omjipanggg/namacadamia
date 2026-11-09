<script setup>
	import Header from "./TableHeader.vue";
	import Body from "./TableBody.vue";
	import { useTable } from "../functions/useTable.js";
	const tableState = useTable();

	defineProps({
		size: {
			type: String,
			default: "",
		},
	});
</script>

<template>
	<div class="border p-1 rounded">
		<div
			:class="[
				'table-responsive',
				'border',
				{
					rounded: tableState.style.rounded,
					border: tableState.style.bordered,
				},
			]"
		>
			<table
				:class="[
					'table',
					'text-nowrap',
					'align-middle',
					'mb-0',
					{
						'table-bordered': tableState.style.bordered,
						'table-hover': tableState.style.hover,
						'table-striped': tableState.style.striped,
					},
					size ? `table-${size}` : '',
				]"
			>
				<Header />
				<Body>
					<template v-for="(i, slotName) in $slots" #[slotName]="slotProps">
						<slot :name="slotName" v-bind="slotProps"></slot>
					</template>
				</Body>
			</table>
		</div>
	</div>
</template>
