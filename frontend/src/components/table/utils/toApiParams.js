export function toApiParams(params) {
	return {
		page: params.page,
		per_page: params.perPage,
		search: params.search,
		sort_by: params.sortBy,
		sort_direction: params.sortDirection,
	};
}
