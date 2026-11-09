import { computed, reactive, ref } from "vue";

export function useResource(service) {
	const lastParams = ref({});
	const resource = ref({});

	const loading = reactive({
		fetch: false,
		create: false,
		update: false,
		destroy: false,
	});

	const errors = reactive({
		fetch: null,
		create: null,
		update: null,
		destroy: null,
	});

	async function fetch(params = {}) {
		lastParams.value = params;
		loading.fetch = true;

		try {
			const response = await service.index(params);
			resource.value = response.resource;
		} catch (error) {
			errors.fetch = error;
			throw error;
		} finally {
			loading.fetch = false;
		}
	}

	async function create(data) {
		loading.create = true;

		try {
			return await service.store(data);
		} catch (error) {
			errors.create = error;
			throw error;
		} finally {
			loading.create = false;
		}
	}

	async function update(id, data) {
		loading.update = true;

		try {
			return await service.update(id, data);
		} catch (error) {
			errors.update = error;
		} finally {
			loading.update = false;
		}
	}

	async function destroy(id) {
		loading.destroy = true;

		try {
			return await service.destroy(id);
		} catch (error) {
			errors.destroy = error;
		} finally {
			loading.destroy = false;
		}
	}

	function refresh() {
		return fetch(lastParams.value);
	}

	const isLoading = computed(() => Object.values(loading).some(Boolean));

	return {
		resource,

		loading,
		isLoading,

		errors,

		fetch,
		create,
		update,
		destroy,
		refresh,
	};
}
