const DEFAULT_COLUMN = {
	cellClass: "",
	displayKey: null,
	formatter: null,
	headerClass: "",
	key: null,
	label: "",
	searchable: true,
	selectable: false,
	separator: ", ",
	sortable: false,
	visible: true,
	width: null,
};

export default function normalizeColumns(columns) {
	return columns.map((column) => ({
		...DEFAULT_COLUMN,
		...column,
	}));
}
