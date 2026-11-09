<script setup>
import { useToastStore } from "@/stores/toast.js";
const toastStore = useToastStore();
</script>

<template>
	<div
		class="toast-container position-fixed top-0 end-0 p-3"
		style="z-index: 9999"
	>
		<TransitionGroup name="toast">
			<div
				v-for="toast in toastStore.toasts"
				:key="toast.id"
				class="toast show mb-2"
				:class="`text-bg-${toast.variant}`"
			>
				<div class="d-flex">
					<div class="toast-body">
						<strong>
							{{ toast.title }}
						</strong>

						<div>
							{{ toast.content }}
						</div>
					</div>

					<button
						type="button"
						class="btn-close btn-close-white me-2 m-auto"
						@click="toastStore.remove(toast.id)"
					></button>
				</div>
			</div>
		</TransitionGroup>
	</div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
	transition: all 0.3s ease;
}

.toast-enter-from,
.toast-leave-to {
	opacity: 0;
	transform: translateX(30px);
}
</style>
