import { defineStore } from "pinia";
import searchService from "@/composables/search.js";

export const useSearchStore = defineStore("search", {
	state: () => ({
		query: "",
		results: [],
		count: 0,
		duration: 0,
		loading: false,
		opened: false,
		selectedIndex: -1,
	}),
	getters: {
		hasResults: (state) => state.results.length > 0,
		isEmpty: (state) =>
			!state.loading &&
			state.query.length >= 2 &&
			state.results.length === 0,
		groupedResults: (state) => {
			return state.results.reduce((groups, item) => {
				if (!groups[item.type]) {
					groups[item.type] = [];
				}

				groups[item.type].push(item);

				return groups;
			}, {});
		},
		selectedResult(state) {
			return state.results[state.selectedIndex] ?? null;
		},
	},
	actions: {
		async search(query) {
			this.query = query.trim();

			if (this.query.length < 2) {
				this.clear();
				return;
			}

			this.loading = true;

			try {
				const response = await searchService.search(this.query);

				this.results = response.resource.data;
				this.count = response.resource.count;
				this.duration = response.resource.duration;

				this.opened = true;
				this.selectedIndex = -1;
			} catch (error) {
				if (
					error.code === "ERR_CANCELED" ||
					error.name === "CanceledError"
				)
					return;
				throw error;
			} finally {
				this.loading = false;
			}
		},
		clear() {
			this.query = "";
			this.results = [];
			this.loading = false;
			this.opened = false;
			this.selectedIndex = -1;
		},
		open() {
			this.opened = true;
		},
		close() {
			this.opened = false;
			this.selectedIndex = -1;
		},
	},
});
