import { defineStore } from "pinia";
import api from "@/services/api.js";

export const usePurchaseStore = defineStore("purchases", {
	state: () => ({
		purchases: [],
		purchaseDetail: null,
		pagination: {
			current_page: 1,
			from: 1,
			last_page: 1,
			per_page: 12,
			to: 1,
			total: 0,
		},
		loading: false,
		error: null,
	}),
	actions: {
		async fetch(params = {}) {
			this.loading = true;
			this.error = null;

			try {
				const response = await api.get("/api/purchases", params);

				this.purchases = response;
				this.pagination = {
					current_page: response?.meta.current_page,
					from: response?.meta.from,
					last_page: response?.meta.last_page,
					per_page: response?.meta.per_page,
					to: response?.meta.to,
					total: response?.meta.total,
				};
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
				const response = await api.get(`/api/purchases/${id}`, params);
				this.purchaseDetail = response.data;
			} catch (error) {
				this.error =
					error.response?.data?.message ?? "Failed to load purchases data.";
				throw error;
			} finally {
				this.loading = false;
			}
		},
	},
});
