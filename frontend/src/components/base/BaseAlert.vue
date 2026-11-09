<script setup>
	import { onUnmounted, computed, ref, watch } from "vue";

	const props = defineProps({
		variant: {
			type: String,
			default: "danger",
			validator: (value) =>
				[
					"primary",
					"secondary",
					"success",
					"danger",
					"warning",
					"info",
					"light",
					"dark",
				].includes(value),
		},
		message: {
			type: [String, Array],
			default: "",
		},
		dismissible: {
			type: Boolean,
			default: true,
		},
		autoDismiss: {
			type: Number,
			default: 0,
		},
		show: {
			type: Boolean,
			default: true,
		},
	});

	const emit = defineEmits(["update:show", "dismissed"]);

	const visible = ref(props.show);
	let timer = null;

	const formattedMessages = computed(() => {
		if (!props.message) return [];
		if (Array.isArray(props.message)) return props.message.flat();
		return [props.message];
	});

	watch(
		() => props.show,
		(newValue) => {
			visible.value = newValue;
			if (newValue) setAutoDismissTimer();
		}
	);

	const iconClass = computed(() => {
		const icons = {
			success: "bi-check-square-fill",
			danger: "bi-x-square-fill",
			warning: "bi-exclamation-square-fill",
			info: "bi-info-square-fill",
			primary: "bi-question-square-fill",
		};
		return icons[props.variant] || null;
	});

	const closeAlert = () => {
		visible.value = false;
		emit("update:show", false);
		emit("dismissed");
		if (timer) clearTimeout(timer);
	};

	const setAutoDismissTimer = () => {
		if (props.autoDismiss > 0) {
			if (timer) clearTimeout(timer);
			timer = setTimeout(() => {
				closeAlert();
			}, props.autoDismiss);
		}
	};

	if (props.show) {
		setAutoDismissTimer();
	}

	onUnmounted(() => {
		if (timer) clearTimeout(timer);
	});
</script>

<template>
	<Transition name="fade">
		<div
			v-if="visible"
			:class="[
				'alert',
				`alert-${variant}`,
				{ 'alert-dismissible': dismissible },
				'd-flex',
				'my-0',
				'align-items-center',
				'gap-3',
				'shadow-sm',
			]"
			role="alert"
		>
			<div v-if="iconClass">
				<i :class="['bi', iconClass, 'fs-5']"></i>
			</div>

			<div class="flex-grow-1">
				<slot>
					<span v-if="formattedMessages.length === 1" class="fw-semibold">
						{{ formattedMessages[0] }}
					</span>

					<ul v-else-if="formattedMessages.length > 1" class="mb-0 ps-3">
						<li v-for="(msg, index) in formattedMessages" :key="index">
							{{ msg }}
						</li>
					</ul>
				</slot>
			</div>

			<button
				v-if="dismissible"
				type="button"
				class="btn-close"
				aria-label="Close"
				@click="closeAlert"
			></button>
		</div>
	</Transition>
</template>

<style scoped>
	.fade-enter-active,
	.fade-leave-active {
		transition:
			opacity 0.25s ease,
			transform 0.25s ease;
	}

	.fade-enter-from,
	.fade-leave-to {
		opacity: 0;
		transform: translateY(-5px);
	}
</style>
