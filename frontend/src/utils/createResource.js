import api from "@/services/api.js";

export default function createResource(endpoint) {
	return {
		index(params = {}) {
			return api.get(endpoint, { params });
		},
		show(id) {
			return api.get(`${endpoint}/${id}`);
		},
		store(data) {
			return api.post(endpoint, data);
		},
		update(id, data) {
			return api.put(`${endpoint}/${id}`, data);
		},
		destroy(id) {
			return api.delete(`${endpoint}/${id}`);
		},
	};
}
