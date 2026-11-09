import { useToastStore } from "@/stores/toast.js";

export function useToast() {
	const toastStore = useToastStore();

	const success = (title, content, timeout = 2048) => {
		toastStore.create({
			title,
			content,
			variant: "success",
			timeout,
		});
	};

	const info = (title, content) => {
		toastStore.create({
			title,
			content,
			variant: "info",
		});
	};

	const warning = (title, content) => {
		toastStore.create({
			title,
			content,
			variant: "warning",
		});
	};

	const danger = (title, content) => {
		toastStore.create({
			title,
			content,
			variant: "danger",
		});
	};

	return {
		success,
		info,
		warning,
		danger,
	};
}
