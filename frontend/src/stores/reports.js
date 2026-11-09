import { defineStore } from "pinia";
import api from "@/services/api.js";

const topProductColumns = [
	{
		key: "sku_code",
		label: "SKU",
	},
	{
		key: "product_name",
		label: "Name",
	},
	{
		key: "orders_count",
		label: "Orders",
	},
	{
		key: "quantity_sold",
		label: "Qty. Sold",
	},
	{
		key: "revenue",
		label: "Revenue",
	},
];

export const useReportStore = defineStore("reports", {
	state: () => ({
		salesReport: [],
		topProductColumns,
		loading: false,
		error: null,
	}),
	getters: {
		summary: (state) => state.salesReport?.summary ?? null,
		salesTrend: (state) => state.salesReport?.sales_trend ?? [],
		salesAndPurchasesTrends: (state) =>
			state.salesReport?.sales_and_purchases_trends ?? [],
		salesByBranch: (state) => state.salesReport?.sales_by_branch ?? [],
		salesByChannel: (state) => state.salesReport?.sales_by_channel ?? [],
		topProducts: (state) => state.salesReport?.top_products ?? [],
		meta: (state) => state.salesReport?.meta ?? null,
	},
	actions: {
		async fetchSalesReport(params = {}) {
			this.loading = true;
			this.error = null;
			try {
				const response = await api.get("/api/reports/sales", params);
				this.salesReport = response;
				return response;
			} catch (error) {
				this.error = error;
				throw error;
			} finally {
				this.loading = false;
			}
		},
	},
});
