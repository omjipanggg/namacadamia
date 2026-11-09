import { defineStore } from "pinia";
import { useAuthStore } from "@/stores/auth.js";
import notificationService from "@/composables/notification.js";

export const useNotificationStore = defineStore("notification", {
	state: () => ({
		initialized: false,
		list: [],
		preview: [],
		unreadCount: 0,
		loading: false,
		hasMore: true,
		page: 1,
	}),
	getters: {
		hasItem: (state) => state.list.length > 0,
		unread: (state) => state.list.filter((item) => !item.read_at),
		read: (state) => state.list.filter((item) => item.read_at),
		total: (state) => state.list.length,
	},
	actions: {
		async initialize() {
			const authStore = useAuthStore();

			if (!authStore.isLoggedIn) return;

			if (this.initialized) return;

			await this.load();

			this.initialized = true;
		},
		async send(params) {
			this.loading = true;

			try {
				const response = await notificationService.send(params);
				const notification = response.resource;

				console.log(!!notification);

				if (notification) {
					this.list.unshift(notification);
					this.preview.unshift(notification);
					this.unreadCount++;
				}
			} catch (error) {
				this.unreadCount = Math.max(0, this.unreadCount - 1);
				console.error(error);
			} finally {
				this.loading = false;
			}
		},
		async load() {
			if (this.loading) return;

			this.loading = true;

			try {
				const temp = await notificationService.preview();
				this.preview = temp.resource?.data;

				const response = await notificationService.fetch();
				this.list = response.resource.data;
				this.page = 1;
				this.hasMore = response.resource.has_more ?? false;
				this.unreadCount = response.resource.unread_count;
			} finally {
				this.loading = false;
			}
		},
		async loadMore() {
			if (!this.hasMore || this.loading) return;

			this.loading = true;

			try {
				const response = await notificationService.fetch({
					page: this.page + 1,
				});

				this.list.push(...response.resource.data);
				this.page++;
				this.hasMore = response.resource.has_more ?? false;
			} catch (error) {
				console.log("loadMore():", error);
			} finally {
				this.loading = false;
			}
		},
		async test() {
			this.loading = true;
			this.unreadCount++;

			try {
				const response = await notificationService.test();
				const notification = response.resource;
				this.list.unshift(notification);
				this.preview.unshift(notification);
			} catch (error) {
				this.unreadCount = Math.max(0, this.unreadCount - 1);
				console.error(error);
			} finally {
				this.loading = false;
			}
		},
		async markAsRead(id) {
			const notification = this.list.find((item) => item.id === id);

			if (!notification || notification.read_at) return;

			notification.read_at = new Date().toISOString();
			this.unreadCount = Math.max(0, this.unreadCount - 1);

			try {
				await notificationService.markAsRead(id);
			} catch (error) {
				notification.read_at = null;
				this.unreadCount++;

				console.error(error);
			}
		},
		async markAllAsRead() {
			await notificationService.markAllAsRead();
			this.list.forEach((notification) => {
				notification.read_at = new Date().toISOString();
			});

			this.unreadCount = 0;
		},
		async remove(id) {
			const notification = this.list.find((item) => item.id === id);

			this.list = this.list.filter((notification) => notification.id !== id);

			this.preview = this.preview.filter(
				(notification) => notification.id !== id
			);

			await notificationService.remove(id);

			if (!notification || notification.read_at) return;
			this.unreadCount = Math.max(0, this.unreadCount - 1);
		},
		clear() {
			this.list = [];
			this.preview = [];
			this.unreadCount = 0;
			this.page = 1;
			this.hasMore = true;
			this.initialized = false;
		},
		listen() {
			console.log("[vite] listening...");
		},
		disconnect() {},
	},
});
