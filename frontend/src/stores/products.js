import { defineStore } from "pinia";
import api from "@/services/api.js";

export const useProductStore = defineStore("products", {
	state: () => ({
		products: [],
		productDetail: [],
		productCategories: [],
		productVariants: [],
		productVariantValues: [],
		loading: false,
		error: null,
	}),
	actions: {
		async withLoading(asyncFn) {
			this.loading = true;
			try {
				return await asyncFn();
			} catch (errorObj) {
				this.error = errorObj;
			} finally {
				this.loading = false;
			}
		},
		async fetch(params = {}) {
			const response = this.withLoading(() =>
				api.get("/api/dashboard/products", params)
			);
			this.products = response.data;
		},
		async fetchDetail(slug, params = {}) {
			const response = this.withLoading(() =>
				api.get(`/api/dashboard/products/${slug}`, params)
			);
			this.productDetail = response.data;
		},
		async fetchAttributes(params = {}) {
			const response = await this.withLoading(() =>
				api.get("/api/dashboard/product-attributes", params)
			);
			this.productVariants = response.data;
		},
		async fetchValues(params = {}) {
			const response = await this.withLoading(() =>
				api.get("/api/dashboard/product-attribute-values", params)
			);
			this.productVariantValues = response.data;
		},
		async updateBulkAttributes(id, params = {}) {
			await this.withLoading(() =>
				api.put(`/api/dashboard/product-attributes/${id}`, params)
			);
		},
		async deleteAttributes(id, params = {}) {
			return await this.withLoading(() =>
				api.delete(`/api/dashboard/product-attributes/${id}`, params)
			);
		},
		async deleteValues(id, params = {}) {
			return await this.withLoading(() =>
				api.delete(`/api/dashboard/product-attribute-values/${id}`, params)
			);
		},
		async fetchCategories(params = {}) {
			const response = await this.withLoading(() =>
				api.get("/api/dashboard/products/category", params)
			);
			this.productCategories = response;
		},
		async createCategory(params = {}) {
			return await this.withLoading(() =>
				api.post("/api/dashboard/products/category", params)
			);
		},
		async updateCategory(slug, params = {}) {
			return await this.withLoading(() =>
				api.put(`/api/dashboard/products/category/${slug}`, params)
			);
		},
		async deleteCategory(slug, params = {}) {
			return await this.withLoading(() =>
				api.delete(`/api/dashboard/products/category/${slug}`, params)
			);
		},
	},
});
