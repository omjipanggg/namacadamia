import { defineStore } from "pinia";
import api from "@/services/api.js";

export const useBranchStore = defineStore("branches", {
	state: () => ({
		branches: [],
		branchDetail: [],
		stocks: [],
		loading: false,
		error: null,
	}),
	actions: {
		async fetch(params = {}) {
			this.clear();

			this.loading = true;
			this.error = null;

			try {
				const response = await api.get("/api/branches", params);
				this.branches = response;
			} catch (error) {
				this.error =
					error.response?.data?.message ?? "Failed to load branches data.";
				throw error;
			} finally {
				this.loading = false;
			}
		},
		async fetchStock(id, params = {}) {
			this.loading = true;
			this.error = null;

			try {
				const response = await api.get(`/api/branches/${id}/stock`, params);
				this.stocks = response;
			} catch (error) {
				this.error =
					error.response?.data?.message ?? "Failed to load stock data.";
			} finally {
				this.loading = false;
			}
		},
		async show(id, params = {}) {
			this.loading = true;
			this.error = null;

			try {
				const response = await api.get(`/api/branches/${id}`, params);
				this.branchDetail = response.data;
			} catch (error) {
				this.error =
					error.response?.data?.message ?? "Failed to load branch details.";
				throw error;
			} finally {
				this.loading = false;
			}
		},
		clear() {
			this.branches = [];
			this.branchDetail = [];
			this.stocks = [];
			this.loading = false;
			this.error = null;
		},
	},
});
