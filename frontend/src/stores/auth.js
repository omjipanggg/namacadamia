import { defineStore } from "pinia";
import authService from "@/services/auth.js";

export const useAuthStore = defineStore("auth", {
	state: () => ({
		user: {},
		loading: true,
		initialized: false,
	}),
	getters: {
		isLoggedIn: (state) => !!state.user,
		isAdmin: (state) =>
			!!state.user?.roles?.some((role) => role.name === "admin"),
	},
	actions: {
		async check() {
			this.loading = true;
			try {
				const response = await authService.check();
				const data = response?.data ?? response;
				this.user = data?.success ? data.resource?.user : null;
			} catch (error) {
				if (error.response?.status === 401) {
					this.user = null;
					return false;
				}

				console.error("backend is not ready, just yet: ", error);
				this.user = null;
				return false;
			} finally {
				this.loading = false;
				this.initialized = true;
			}
		},
		async login(credentials) {
			this.loading = true;
			try {
				const response = await authService.login(credentials);
				await this.check();
				return response.data;
			} finally {
				this.loading = false;
			}
		},
		async logout() {
			this.loading = true;
			try {
				await authService.logout();
				this.user = null;
			} finally {
				this.user = null;
				this.initialized = false;
				this.loading = false;
			}
		},
	},
});
