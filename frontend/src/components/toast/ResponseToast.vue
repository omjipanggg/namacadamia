<!-- eslint-disable vue/no-v-html -->
<script setup>
	import { useI18n } from "vue-i18n";
	import { useToastStore } from "@/stores/toast.js";
	const toastStore = useToastStore();
	const { te, t } = useI18n();
	const sanitized = (msg) => {
		if (!msg) return;

		if (typeof msg === "object" && msg.key) {
			return te(msg.key) ? t(msg.key, msg.params || {}) : msg.key;
		}

		return te(msg) ? t(msg) : msg;
	};
</script>

<template>
	<div aria-live="polite" aria-atomic="true" class="position-relative">
		<div class="toast-container top-0 end-0 p-3 position-fixed">
			<TransitionGroup name="toast">
				<div
					v-for="toast in toastStore.toasts"
					:key="toast.id"
					class="toast show mb-2"
					role="alert"
					aria-live="assertive"
					aria-atomic="true"
				>
					<div class="toast-header">
						<span
							class="rounded me-2 px-2 squared d-block"
							:class="`bg-${toast.variant}`"
						>
							&nbsp;
						</span>
						<!-- eslint-disable-next-line vue/no-v-html -->
						<strong class="me-auto" v-html="sanitized(toast.title)"></strong>
						<!-- <small class="text-body-secondary">{{ $t("time.now") }}</small> -->
						<button
							type="button"
							class="btn-close"
							data-bs-dismiss="toast"
							aria-label="Close"
							@click="toastStore.remove(toast.id)"
						></button>
					</div>
					<div class="toast-body">
						<!-- eslint-disable-next-line vue/no-v-html -->
						<span v-html="sanitized(toast.content)"></span>
					</div>
				</div>
			</TransitionGroup>
		</div>
	</div>
</template>

<style scoped>
	.toast-enter-active,
	.toast-leave-active {
		transition: all 320ms ease;
	}

	.toast-enter-from,
	.toast-leave-to {
		opacity: 0;
		transform: translateX(32px);
	}
</style>
