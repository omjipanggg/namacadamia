import { defineStore } from "pinia";
import i18n from "../helpers/locale.js";

export const useToastStore = defineStore("toast", {
	state: () => ({
		toasts: [],
	}),
	actions: {
		create(payload) {
			const { title, content, variant = "success", timeout = 2048 } = payload;

			const id = Date.now();

			const isObject = typeof content === "object" && content !== null;

			this.toasts.push({
				id,
				title: title && i18n.global.te(title) ? i18n.global.t(title) : title,
				content: isObject
					? i18n.global.t(content.key, content.params)
					: i18n.global.te(content)
						? i18n.global.t(content)
						: content,
				variant,
			});

			setTimeout(() => {
				this.toasts = this.toasts.filter((toast) => toast.id != id);
			}, timeout);
		},
		remove(index) {
			this.toasts.splice(index, 1);
		},
	},
});
