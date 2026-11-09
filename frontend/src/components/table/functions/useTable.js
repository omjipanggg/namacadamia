import { inject } from "vue";
import { TABLE_KEY } from "../utils/variables.js";

export function useTable() {
	const state = inject(TABLE_KEY);

	if (!state) {
		throw new Error("useTable() must be used inside <Table/> component.");
	}

	return state;
}
