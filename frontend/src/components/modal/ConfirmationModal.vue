<script setup>
	import { onMounted, ref, watch } from "vue";
	import { useI18n } from "vue-i18n";
	import { useModalStore } from "@/stores/modal.js";
	import { Modal } from "bootstrap";

	const modalStore = useModalStore();
	const { te, t } = useI18n();

	const modalElement = ref(null);

	let modal = null;

	const sanitized = (msg) => {
		if (!msg) return;

		if (typeof msg === "object" && msg.key) {
			return te(msg.key) ? t(msg.key, msg.params || {}) : msg.key;
		}

		return te(msg) ? t(msg) : msg;
	};

	onMounted(() => {
		modal = new Modal(modalElement.value);

		modalElement.value.addEventListener("hidden.bs.modal", () => {
			if (modalStore.visible) {
				modalStore.cancel();
			}
		});
	});

	watch(
		() => modalStore.visible,
		(visible) => {
			if (!modal) return;

			if (visible) {
				modal.show();
			} else {
				document.activeElement?.blur();
				modal.hide();
			}
		}
	);
</script>

<template>
	<div ref="modalElement" class="modal fade" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						<!-- eslint-disable-next-line vue/no-v-html -->
						<span v-html="sanitized(modalStore.title)"></span>
					</h5>
					<button
						type="button"
						class="btn btn-close"
						@click="modalStore.cancel()"
					></button>
				</div>
				<div class="modal-body">
					<!-- eslint-disable-next-line vue/no-v-html -->
					<span v-html="sanitized(modalStore.message)"></span>
				</div>
				<div class="modal-footer">
					<button
						class="btn btn-secondary"
						type="button"
						@click="modalStore.cancel()"
					>
						{{
							$te(modalStore.cancelText)
								? $t(modalStore.cancelText)
								: modalStore.cancelText
						}}
					</button>
					<button
						class="btn px-4 btn-primary"
						type="button"
						@click="modalStore.confirm()"
					>
						{{
							$te(modalStore.confirmText)
								? $t(modalStore.confirmText)
								: modalStore.confirmText
						}}
					</button>
				</div>
			</div>
		</div>
	</div>
</template>
