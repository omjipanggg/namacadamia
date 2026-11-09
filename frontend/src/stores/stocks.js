import { defineStore } from "pinia";
import api from "@/services/api.js";

export const useStockStore = defineStore("stocks", {
	state: () => ({
		stocks: [],
		stockDetail: null,
		transfers: [],
		currentTransfer: null,
		loading: false,
		saving: false,
		error: null,
	}),
	actions: {
		async fetch(params = {}) {
			this.loading = true;
			this.error = null;

			try {
				const response = await api.get("/api/stocks", params);
				this.stocks = response;
				this.stocksByBranches = response.stocks_by_branches;
			} catch (error) {
				this.error =
					error.response?.data?.message ?? "Failed to load purchases data.";
				throw error;
			} finally {
				this.loading = false;
			}
		},
		async show(id, params = {}) {
			this.loading = true;
			this.error = null;

			try {
				const response = await api.get(`/api/stocks/${id}`, params);
				this.stockDetail = response.data;
			} catch (error) {
				this.error =
					error.response?.data?.message ?? "Failed to load purchases data.";
				throw error;
			} finally {
				this.loading = false;
			}
		},
		async adjust(params = {}) {
			this.loading = true;
			this.error = null;

			try {
				await api.post(`/api/stocks/adjust`, params);
			} catch (error) {
				this.error =
					error.response?.data?.message ?? "Failed to perform stock opname.";
				throw error;
			} finally {
				this.loading = false;
			}
		},
		async fetchTransfers(params = {}) {
			this.loading = true;

			try {
				const response = await api.get("/api/stock-transfer", params);
				this.transfers = response;

				return response;
			} finally {
				this.loading = false;
			}
		},
		async showTransfer(id) {
			this.loading = true;

			try {
				const response = await api.get(`/api/stock-transfer/${id}`);
				this.currentTransfer = response.data;

				return response.data;
			} finally {
				this.loading = false;
			}
		},
		async createTransfer(payload) {
			this.saving = true;

			try {
				const response = await api.post("/api/stock-transfer", payload);
				this.currentTransfer = response.data;

				return response.data;
			} finally {
				this.saving = false;
			}
		},
		async completeTransfer(id) {
			this.loading = true;

			try {
				const response = await api.post(`/api/stock-transfer/${id}/complete`);
				this.currentTransfer = response.data;
				return response.data;
			} finally {
				this.loading = false;
			}
		},
	},
});
