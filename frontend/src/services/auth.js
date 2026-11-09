import { retrieveCsrfCookie } from "@/services/csrf.js";
import instance from "@/helpers/axios.js";

export default {
	async login(data) {
		await retrieveCsrfCookie();
		return instance.post("/api/login", data);
	},
	async register(data) {
		await retrieveCsrfCookie();
		return instance.post("/api/register", data);
	},
	async logout() {
		return instance.post("/api/logout");
	},
	async isLoggedIn() {
		const response = await instance.get("/api/check");
		return response.data.success;
	},
	isGoogleAuthenticated() {
		return instance.get("/api/auth/google/check");
	},
	check() {
		return instance.get("/api/check");
	},
};
