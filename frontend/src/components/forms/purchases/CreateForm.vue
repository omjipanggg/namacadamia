<script setup>
	import { computed, reactive } from "vue";
	import { formatToInputString } from "@/helpers/format.js";

	import DetailCard from "./CreateDetailCard.vue";
	import ItemCard from "./CreateItemCard.vue";

	defineProps({
		errors: {
			type: Object,
			default: () => ({}),
		},
		lookup: {
			type: Object,
			default: () => ({}),
		},
	});

	const emit = defineEmits(["save"]);

	const createInitialForm = () => ({
		stock_location_id: 1,
		supplier_id: 1,
		ordered_at: formatToInputString(),
		auto_receive: false,
		notes: null,
		items: [],
	});

	const form = reactive(createInitialForm());

	const addItem = () => {
		form.items.push({
			id: crypto.randomUUID(),
			product_sku_id: null,
			measure_unit_id: 1,
			quantity: 1,
			cost_price: 0,
		});
	};

	const removeItem = (id) => {
		form.items = form.items.filter((item) => item.id !== id);
	};

	const selectedSkuIds = computed(() =>
		form.items.map((item) => item.product_sku_id).filter(Boolean)
	);

	function submit() {
		emit("save", form);
	}

	function reset() {
		Object.assign(form, createInitialForm());
	}

	defineExpose({
		reset,
		submit,
	});
</script>

<template>
	<form @submit.prevent="submit">
		<div class="d-flex flex-column gap-3">
			<DetailCard
				v-model:auto-receive="form.auto_receive"
				v-model:ordered-at="form.ordered_at"
				v-model:supplier-id="form.supplier_id"
				v-model:stock-location-id="form.stock_location_id"
				v-model:notes="form.notes"
				:lookup="lookup"
				:errors="errors"
			/>
			<ItemCard
				v-model="form.items"
				:lookup="lookup"
				:excluded-ids="selectedSkuIds"
				:heading="form.items.length === 0"
				@add="addItem"
				@remove="removeItem"
			/>
		</div>
	</form>
</template>
