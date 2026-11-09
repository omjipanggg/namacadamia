<script setup>
	import { computed, ref } from "vue";

	defineEmits(["action"]);

	const props = defineProps({
		items: {
			type: Array,
			default: () => [],
		},
		placeholder: {
			type: String,
			default: "Select...",
		},
		taggable: {
			type: Object,
			default: () => ({}),
		},
		disabled: {
			type: Boolean,
			default: false,
		},
		label: {
			type: String,
			default: "",
		},
	});

	const model = defineModel({
		type: [String, Number, null],
		default: null,
	});

	const opened = ref(false);
	const keyword = ref("");

	const selected = computed(() =>
		props.items.find((item) => item.value === model.value)
	);

	const filteredItems = computed(() => {
		if (!keyword.value) {
			return props.items;
		}

		const search = keyword.value.toLowerCase();

		return props.items.filter(
			(item) =>
				item.title.toLowerCase().includes(search) ||
				item.subtitle?.toLowerCase().includes(search)
		);
	});

	const toggle = () => {
		if (props.disabled) return;
		opened.value = !opened.value;
	};

	const select = (item) => {
		model.value = item.value;
		keyword.value = "";
		opened.value = false;
	};
</script>

<template>
	<div>
		<div class="lookup-select">
			<span v-if="label" class="form-label mb-2 d-block">{{ label }}</span>
			<div
				class="form-select text-truncate"
				:class="disabled ? 'cursor-disabled' : 'cursor-pointer'"
				@click="toggle"
			>
				<span v-if="selected" class="text-truncate" :title="selected.title">
					{{ selected.title }} | {{ selected.subtitle }}
				</span>
				<span v-else class="text-muted">
					{{ placeholder }}
				</span>
			</div>

			<div v-if="opened" class="lookup-menu">
				<div class="lookup-item-searchable">
					<input
						:id="`lookup-select-opened-${label.toLowerCase()}-2`"
						v-model="keyword"
						type="text"
						class="form-control rounded-0 py-3 text-truncate"
						:placeholder="$t('search.title')"
					/>
				</div>
				<div class="lookup-item-scrollable">
					<div v-if="items.length === 0" class="lookup-no-item">
						{{ $t("select.noOptions") }}
					</div>
					<div
						v-for="item in filteredItems"
						v-else
						:key="item.value"
						class="lookup-item"
						@click="select(item)"
					>
						<div>
							{{ item.title }}
						</div>

						<small v-if="item.subtitle" class="text-muted">
							{{ item.subtitle }}
						</small>
					</div>
				</div>
				<div
					v-if="taggable?.label"
					class="lookup-item"
					@click.stop="$emit('action', taggable)"
				>
					<i class="bi bi-plus-square me-2"></i>
					{{ $t("common.addNew", { item: taggable.label }) }}
				</div>
			</div>
		</div>
		<div v-if="opened" class="lookup-backdrop" @click="toggle"></div>
	</div>
</template>

<style scoped>
	.lookup-select {
		position: relative;
	}

	.lookup-menu {
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		z-index: 1050;

		background-color: var(--bs-body-bg);

		width: 100%;
	}

	.lookup-no-item {
		padding: 2rem 1rem;
	}

	.lookup-item-scrollable {
		max-height: 336px;

		width: 100%;
		overflow-y: auto;
	}

	.lookup-item {
		padding: 0.6rem 0.8rem;
		cursor: pointer;
	}

	.lookup-item:hover {
		background-color: var(--bs-secondary-bg);
	}

	.form-grow-1 {
		overflow: hidden;
		text-overflow: ellipsis;
		min-width: 270px;
		/* max-width: 270px; */
		width: 100%;
	}

	.lookup-backdrop {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100vh;
		z-index: 1040;
		background-color: hsla(0deg, 0%, 0%, 0.8);
	}
</style>
