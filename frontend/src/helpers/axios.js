import axios from "axios";

const instance = axios.create({
	baseURL: import.meta.env.VITE_APP_BACKEND_URL,
	maxContentLength: 50 * 1000 * 1000,
	withCredentials: true,
	withXSRFToken: true,
	timeout: 10000,
	maxRedirects: 10,
	xsrfCookieName: "XSRF-TOKEN",
	xsrfHeaderName: "X-XSRF-TOKEN",
});

export default instance;
