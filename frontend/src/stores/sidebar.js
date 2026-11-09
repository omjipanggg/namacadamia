import { defineStore } from "pinia";

export const useSidebarStore = defineStore("sidebar", {
	state: () => ({
		collapsed: false,
		mobileOpen: false,
	}),
	getters: {
		isMobile: () => window.innerWidth < 992,
	},
	actions: {
		toggle() {
			if (window.innerWidth < 992) {
				this.mobileOpen = !this.mobileOpen;
			} else {
				this.collapsed = !this.collapsed;
			}
		},
		collapse() {
			this.collapsed = true;
		},
		expand() {
			this.collapsed = false;
		},
		toggleMobile() {
			this.mobileOpen = !this.mobileOpen;
		},
		openMobile() {
			this.mobileOpen = true;
		},
		closeMobile() {
			this.mobileOpen = false;
		},
	},
});
