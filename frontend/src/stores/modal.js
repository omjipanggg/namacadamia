import { ref } from "vue";
import { defineStore } from "pinia";

export const useModalStore = defineStore("modal", () => {
	const title = ref("");
	const message = ref("");
	const confirmText = ref("Confirm");
	const cancelText = ref("Cancel");

	const visible = ref(false);

	let resolver = null;

	function open(options = {}) {
		title.value = options.title;
		message.value = options.message;
		confirmText.value = options.confirmText;
		cancelText.value = options.cancelText;

		visible.value = true;

		return new Promise((resolve) => {
			resolver = resolve;
		});
	}

	function confirm() {
		visible.value = false;
		resolver?.(true);
		resolver = null;
	}

	function cancel() {
		visible.value = false;
		resolver?.(false);
		resolver = null;
	}

	return {
		title,
		message,
		confirmText,
		cancelText,
		visible,
		open,
		confirm,
		cancel,
	};
});
