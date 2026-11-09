import { defineStore } from "pinia";
import router from "@/router/index.js";
import api from "@/services/api.js";

export const useSaleStore = defineStore("sales", {
	state: () => ({
		sales: [],
		saleDetail: null,
		receipt: null,
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
				const response = await api.get("/api/sales", params);

				this.sales = response;
				this.pagination = {
					current_page: response?.meta.current_page,
					from: response?.meta.from,
					last_page: response?.meta.last_page,
					per_page: response?.meta.per_page,
					to: response?.meta.to,
					total: response?.meta.total,
				};
			} catch (error) {
				this.error = error.response?.data;
				throw error;
			} finally {
				this.loading = false;
			}
		},
		async show(id, params = {}) {
			this.loading = true;
			this.error = null;

			try {
				const response = await api.get(`/api/sales/${id}/`, params);
				this.saleDetail = response.data;
			} catch (error) {
				if (error.response && error.response.status === 404) {
					return router.replace({ name: "lost.index" });
				}
				this.error = error.response?.data;
				throw error;
			} finally {
				this.loading = false;
			}
		},
		async addPayment(id, params = {}) {
			this.loading = true;
			this.error = null;

			try {
				const response = await api.post(`/api/sales/${id}/pay`, params);
				this.receipt = response;
			} catch (error) {
				this.error = error.response?.data;
				throw error;
			} finally {
				this.loading = false;
			}
		},
		async complete(id, params = {}) {
			this.loading = true;
			this.error = null;

			try {
				const response = await api.post(`/api/sales/${id}/complete`, params);
				this.saleDetail = response.data;
			} catch (error) {
				this.error = error.response?.data;
				throw error;
			} finally {
				this.loading = false;
			}
		},
	},
});
