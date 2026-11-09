import { defineStore } from "pinia";
import api from "@/services/api.js";

const LOOKUP_KEYS = [
	"branches",
	"suppliers",
	"customers",
	"skus",
	"units",
	"sales_channels",
	"payment_methods",
	"users",
	"roles",
	"permissions",
	"product_attribute_values",
	"product_attributes",
	"product_categories",
	"stock_locations",
	"stocks",
];

const createEmptyState = () =>
	Object.fromEntries(LOOKUP_KEYS.map((key) => [key, []]));

const createBooleanState = (value = false) =>
	Object.fromEntries(LOOKUP_KEYS.map((key) => [key, value]));

export const useLookupStore = defineStore("lookup", {
	state: () => ({
		...createEmptyState(),
		loading: createBooleanState(),
		loaded: createBooleanState(),
	}),
	getters: {
		isLoading: (state) => (lookup) => !!state.loading[lookup],
		isLoaded: (state) => (lookup) => !!state.loaded[lookup],
	},
	actions: {
		async load(includes = [], force = false) {
			includes = [...new Set(includes)].filter((lookup) =>
				LOOKUP_KEYS.includes(lookup)
			);

			const missing = force
				? includes
				: includes.filter((item) => !this.loaded[item]);

			if (!missing.length) {
				return this;
			}

			missing.forEach((item) => {
				this.loading[item] = true;
			});

			try {
				const data = await api.get("/api/dashboard/lookups", {
					include: missing.join(","),
				});
				Object.entries(data).forEach(([key, values]) => {
					if (!LOOKUP_KEYS.includes(key)) {
						return;
					}

					this[key] = values;
					this.loaded[key] = true;
				});

				return data;
			} finally {
				missing.forEach((item) => {
					this.loading[item] = false;
				});
			}
		},
		async refresh(lookup) {
			return await this.load([lookup], true);
		},
		clear(lookup) {
			if (!LOOKUP_KEYS.includes(lookup)) {
				return;
			}

			this[lookup] = [];
			this.loading[lookup] = false;
			this.loaded[lookup] = false;
		},
		reset() {
			Object.assign(this, createEmptyState());

			this.loading = createBooleanState();
			this.loaded = createBooleanState();
		},
	},
});
