<script setup>
	const isOpen = defineModel({ type: Boolean, default: false });

	defineProps({
		imageSrc: {
			type: String,
			required: true,
		},
		altText: {
			type: String,
			default: "Image",
		},
	});
</script>

<template>
	<div
		v-if="isOpen"
		class="modal fade has-backdrop m-0 p-0"
		:class="{ show: isOpen, 'd-block': isOpen }"
		tabindex="-1"
		role="dialog"
		@click.self="isOpen = false"
	>
		<div
			class="modal-dialog text-center p-3 border-0 shadow-lg rounded"
			role="modal"
		>
			<div class="modal-content">
				<div class="modal-body p-3">
					<img
						:src="imageSrc"
						:alt="altText"
						class="img-fluid rounded mb-3 shadow-sm max-img-height"
					/>
					<h5 v-if="altText" class="fw-semibold mb-0 text-muted">
						{{ altText }}
					</h5>
				</div>
				<div class="modal-footer justify-content-center border-0 pt-0 p-3">
					<button
						type="button"
						class="btn btn-primary px-5 py-2 rounded-pill fw-semibold"
						@click="isOpen = false"
					>
						{{ $t("common.okay") }}
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
	.has-backdrop {
		background-color: rgba(0, 0, 0, 0.69);
		backdrop-filter: blur(2px);
	}
	.max-img-height {
		max-height: 60vh;
		object-fit: contain;
	}
</style>
