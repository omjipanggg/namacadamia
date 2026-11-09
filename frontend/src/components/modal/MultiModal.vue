<script setup>
	import { computed, ref } from "vue";
	import { useModal } from "@/composables/modal.js";

	import BaseButton from "@/components/base/BaseButton.vue";

	const {
		isActive,
		currentComponent,
		modalProps,
		modalOptions,
		openModal,
		closeModal,
	} = useModal();

	const modalSize = computed(() => {
		return modalOptions.value?.size ? `modal-${modalOptions.value?.size}` : "";
	});

	const formRef = ref(null);

	const triggerReset = () => {
		formRef.value?.reset?.();
	};

	const triggerSubmit = () => {
		formRef.value?.submit?.();
	};
</script>

<template>
	<Teleport to="body">
		<Transition name="fade">
			<div
				v-show="isActive"
				class="modal fade"
				:class="{ 'show d-block': isActive }"
				tabindex="-1"
				role="dialog"
				@click.self="closeModal"
			>
				<div
					class="modal-dialog modal-dialog-scrollable"
					:class="modalSize"
					role="modal"
				>
					<div
						:class="[
							'modal-content',
							{ 'overflow-visible': modalOptions.overflowVisible },
						]"
					>
						<div v-if="modalOptions?.title" class="modal-header">
							<h5 class="modal-title">
								<slot name="title">
									{{
										$te(modalOptions.title)
											? $t(modalOptions.title)
											: modalOptions.title
									}}
								</slot>
							</h5>
							<button
								type="button"
								class="btn-close"
								aria-label="Close"
								:disabled="modalProps.loading"
								@click="closeModal"
							></button>
						</div>
						<div
							:class="[
								'modal-body',
								{ 'overflow-visible': modalOptions.overflowVisible },
							]"
						>
							<component
								:is="currentComponent"
								ref="formRef"
								v-bind="modalProps"
								@close="closeModal"
							/>
						</div>
						<div
							v-if="$slots.footer || modalOptions.footer"
							class="modal-footer w-100 align-items-center justify-content-between"
						>
							<slot
								name="footer"
								:submit="triggerSubmit"
								:reset="triggerReset"
								:close="closeModal"
							>
								<BaseButton
									variant="danger"
									:disabled="modalProps.loading"
									@click="triggerReset"
								>
									{{ $t("common.reset") }}
									<i class="bi bi-arrow-repeat ms-2"></i>
								</BaseButton>
								<div class="d-flex flex-wrap gap-2">
									<BaseButton
										variant="secondary"
										:disabled="modalProps.loading"
										@click="closeModal"
									>
										{{ $t("common.cancel") }}
										<i class="bi bi-x-lg ms-2"></i>
									</BaseButton>
									<BaseButton
										variant="primary"
										:disabled="modalProps.loading"
										:loading="modalProps.loading"
										@click="triggerSubmit"
									>
										{{ $t("common.save") }}
										<i class="bi bi-floppy ms-2"></i>
									</BaseButton>
								</div>
							</slot>
						</div>
					</div>
				</div>
			</div>
		</Transition>

		<div
			v-show="isActive"
			class="modal-backdrop fade"
			:class="{ show: isActive, 'is-opening': openModal && false }"
		></div>
	</Teleport>
</template>
