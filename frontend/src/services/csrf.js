import instance from "@/helpers/axios.js";

let csrfReady = false;
let pendingRequest = null;

export async function retrieveCsrfCookie(force = false) {
	if (force) {
		csrfReady = false;
		pendingRequest = null;
	}

	if (csrfReady) return;

	if (!pendingRequest) {
		pendingRequest = instance
			.get("/sanctum/csrf-cookie")
			.then(() => {
				csrfReady = true;
			})
			.finally(() => {
				pendingRequest = null;
			});
	}

	return pendingRequest;
}

export function resetCsrfCookie() {
	csrfReady = false;
	pendingRequest = null;
}
