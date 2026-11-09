import getNestedValue from "./getNestedValue.js";

export default function resolveCellValue(row, column) {
	const value = getNestedValue(row, column.key);

	if (typeof column.formatter === "function") {
		return column.formatter(row);
	}

	if (Array.isArray(value)) {
		if (!column.displayKey) {
			return value.join(column.separator ?? ", ");
		}
		return value
			.map((item) => getNestedValue(item, column.displayKey))
			.join(column.separator ?? ", ");
	}

	return value;
}
