import { defineStore } from "pinia";

let interval = null;

export const useLoadingStore = defineStore("loading", {
	state: () => ({
		loading: false,
		progress: 0,

		activeRequests: 0,
		navigating: false,
	}),
	actions: {
		start() {
			if (this.loading) return;

			this.loading = true;
			this.progress = 0;

			let step = 0.5;

			clearInterval(interval);

			interval = setInterval(() => {
				if (this.progress < 96) {
					this.progress += step;
					step += 0.5;
				} else {
					clearInterval(interval);
				}
			}, 120);
		},

		finish() {
			clearInterval(interval);

			this.progress = 100;

			setTimeout(() => {
				this.loading = false;
				this.progress = 0;
			}, 240);
		},

		requestStarted() {
			this.activeRequests++;

			if (this.activeRequests === 1) {
				this.start();
			}
		},

		requestFinished() {
			this.activeRequests--;

			if (this.activeRequests <= 0) {
				this.activeRequests = 0;

				if (!this.navigating) {
					this.finish();
				}
			}
		},

		navigationStarted() {
			this.navigating = true;
			this.start();
		},

		navigationFinished() {
			this.navigating = false;

			if (this.activeRequests === 0) {
				this.finish();
			}
		},
	},
});
