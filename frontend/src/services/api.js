import instance from "@/helpers/axios.js";
import { retrieveCsrfCookie, resetCsrfCookie } from "@/services/csrf.js";
import { useLoadingStore } from "@/stores/loading.js";

const PROTECTED_METHODS = ["post", "put", "patch", "delete"];

async function performRequest(method, url, data = {}, config = {}) {
	const loading = useLoadingStore();

	loading.requestStarted();

	const requestConfig = { ...config, method, url };

	if (PROTECTED_METHODS.includes(method.toLowerCase())) {
		requestConfig.data = data;
	} else {
		requestConfig.params = data;
	}

	try {
		return await instance(requestConfig);
	} finally {
		loading.requestFinished();
	}
}

async function request(method, url, data = {}, config = {}) {
	const isMethodProtected = PROTECTED_METHODS.includes(method.toLowerCase());

	if (isMethodProtected) {
		await retrieveCsrfCookie();
	}

	try {
		const response = await performRequest(method, url, data, config);
		return response.data;
	} catch (error) {
		if (error.name === "CanceledError" || error.code === "ERR_CANCELED") {
			throw error;
		}

		if (error.response?.status === 419 && isMethodProtected) {
			resetCsrfCookie();
			await retrieveCsrfCookie(true);
			const retry = await performRequest(method, url, data, config);
			return retry.data;
		}

		// console.error(error.response?.data ?? error.message);
		throw error;
	}
}

export default {
	request,
	get(url, params = {}, config = {}) {
		return request("get", url, params, config);
	},
	post(url, data = {}, config = {}) {
		return request("post", url, data, config);
	},
	put(url, data = {}, config = {}) {
		return request("put", url, data, config);
	},
	patch(url, data = {}, config = {}) {
		return request("patch", url, data, config);
	},
	delete(url, params = {}, config = {}) {
		return request("delete", url, params, config);
	},
};
