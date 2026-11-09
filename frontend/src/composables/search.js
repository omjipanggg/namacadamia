import api from "@/services/api.js";

let controller = null;

export default {
	async search(query, limit = 3) {
		if (controller) {
			controller.abort();
		}

		const currentController = new AbortController();
		controller = currentController;

		try {
			return await api.get(
				"/api/search",
				{
					query,
					limit,
				},
				{ signal: currentController.signal },
			);
		} finally {
			if (controller === currentController) {
				controller = null;
			}
		}
	},
	searchRoute(result) {
		return {
			name: `${result.type}.show`,
			params: { slug: result.slug },
		};
	},
};
