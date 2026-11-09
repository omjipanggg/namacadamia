<script setup>
	import { computed, onMounted, onBeforeUnmount } from "vue";

	const open = defineModel({
		type: Boolean,
		default: false,
	});

	const props = defineProps({
		title: {
			type: String,
			default: "",
		},
		size: {
			type: String,
			default: "md",
		},
		closeOnBackdrop: {
			type: Boolean,
			default: true,
		},
		closeOnEsc: {
			type: Boolean,
			default: true,
		},
		closeOnSave: {
			type: Boolean,
			default: true,
		},
		loading: {
			type: Boolean,
			default: false,
		},
		persistent: {
			type: Boolean,
			default: false,
		},
		overflowVisible: {
			type: Boolean,
			default: false,
		},
		centerFooter: {
			type: Boolean,
			default: false,
		},
	});

	const emit = defineEmits(["close"]);

	const modalSize = computed(() => {
		return {
			xs: "modal-xs",
			sm: "modal-sm",
			md: "",
			lg: "modal-lg",
			xl: "modal-xl",
		}[props.size];
	});

	const footerClass = computed(() => {
		return props.centerFooter
			? "justify-content-center"
			: "justify-content-between";
	});

	function close() {
		open.value = false;
		emit("close");
	}

	function onBackdropClick(event) {
		if (props.persistent) return;
		if (!props.closeOnBackdrop) return;

		if (event.target === event.currentTarget) {
			close();
		}
	}

	function escKeyListener(event) {
		if (props.persistent) return;
		if (!props.closeOnEsc) return;

		if (event.key === "Escape") {
			close();
		}
	}

	onMounted(() => {
		document.addEventListener("keydown", escKeyListener);
	});

	onBeforeUnmount(() => {
		document.removeEventListener("keydown", escKeyListener);
	});
</script>

<template>
	<Teleport defer to="body">
		<Transition name="fade">
			<div
				v-show="open"
				class="modal fade"
				:class="{ show: open, 'd-block': open }"
				tabindex="-1"
				role="dialog"
				@click="onBackdropClick"
			>
				<div
					class="modal-dialog modal-dialog-scrollable"
					:class="modalSize"
					role="modal"
				>
					<div
						:class="['modal-content', { 'overflow-visible': overflowVisible }]"
					>
						<div v-if="title" class="modal-header">
							<h5 class="modal-title">
								<slot name="title">
									{{ title }}
								</slot>
							</h5>
							<button
								type="button"
								class="btn-close"
								aria-label="Close"
								:disabled="loading"
								@click="close"
							></button>
						</div>
						<div
							:class="['modal-body', { 'overflow-visible': overflowVisible }]"
						>
							<slot></slot>
						</div>
						<div
							v-if="$slots.footer"
							class="modal-footer w-100 align-items-center"
							:class="footerClass"
						>
							<slot name="footer"></slot>
						</div>
					</div>
				</div>
			</div>
		</Transition>

		<div
			v-show="open"
			class="modal-backdrop fade"
			:class="{ show: open }"
		></div>
	</Teleport>
</template>
