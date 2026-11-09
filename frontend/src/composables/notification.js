import api from "@/services/api.js";
import { retrieveCsrfCookie } from "@/services/csrf.js";

const notificationService = {
	async preview() {
		return await api.get("/api/notifications/preview");
	},
	async send(params = {}) {
		return await api.post("/api/notifications", params);
	},
	async fetch(params = {}) {
		return await api.get("/api/notifications", params);
	},
	async markAsRead(id) {
		await retrieveCsrfCookie();
		return await api.patch(`/api/notifications/${id}/read`);
	},
	async markAllAsRead() {
		await retrieveCsrfCookie();
		return await api.patch("/api/notifications/read-all");
	},
	async test() {
		return await api.post("/api/notifications/test");
	},
	async remove(id) {
		return await api.delete(`/api/notifications/${id}`);
	},
	async removeAll() {
		return await api.delete("/api/notifications");
	},
};

export default notificationService;
