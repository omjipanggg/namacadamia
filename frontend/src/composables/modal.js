import { ref, shallowRef } from "vue";

const isActive = ref(false);
const currentComponent = shallowRef(null);
const modalProps = ref({});
const modalOptions = ref({
	title: "",
	size: "md",
	footer: true,
});

export const useModal = () => {
	const openModal = (component, props = {}, options = {}) => {
		currentComponent.value = component;
		modalProps.value = props;
		modalOptions.value = options;
		isActive.value = true;
	};

	const closeModal = () => {
		isActive.value = false;
		setTimeout(() => {
			currentComponent.value = null;
			modalProps.value = {};
			modalOptions.value = {};
		}, 240);
	};

	return {
		isActive,
		currentComponent,
		modalProps,
		modalOptions,
		openModal,
		closeModal,
	};
};
