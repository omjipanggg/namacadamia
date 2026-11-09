import createResource from "@/utils/createResource.js";
import i18n from "@/helpers/locale.js";

export const columns = [
	{
		key: "name",
		label: i18n.global.t("columns.name"),
		sortable: true,
	},
	{
		key: "categories",
		label: i18n.global.t("columns.categories"),
		displayKey: "name",
	},
	{
		key: "formatted_price",
		label: i18n.global.t("columns.price"),
		sortable: true,
	},
	{
		key: "skus_count",
		label: "SKU",
		sortable: true,
	},
	// {
	// 	key: "image_url",
	// 	label: i18n.global.t("columns.image"),
	// 	sortable: false,
	// 	formatter: (row) => {
	// 		return `<a href="${row.image_url}" alt="image-${row.id}">Image</a>`;
	// 	},
	// },
	{
		key: "description",
		label: i18n.global.t("columns.description"),
		sortable: true,
	},
	{
		key: "slug",
		label: i18n.global.t("common.actions"),
	},
];

export const generateRandomCode = (length, options = {}) => {
	const isOptionsObject = typeof options === "object" && options !== null;

	const alpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	const numeric = "0123456789";

	let chars = "";

	if (isOptionsObject ? (options.letters ?? true) : true) chars += alpha;
	if (isOptionsObject ? (options.numbers ?? true) : Boolean(options))
		chars += numeric;

	if (!chars) {
		throw new Error(
			"At least one character set (letters or numbers) must be true."
		);
	}

	let result = "";

	for (let i = 0; i < length; i++) {
		const randomIndex = Math.floor(Math.random() * chars.length);
		result += chars[randomIndex];
	}
	return result;
};

export const generateVariants = (groups, prefix) => {
	let combinations = [[]];

	for (const group of groups) {
		const next = [];
		for (const combination of combinations) {
			for (const value of group.values) {
				next.push([...combination, value]);
			}
		}
		combinations = next;
	}

	return combinations.map((values) => ({
		key: values
			.map((val) => val.value)
			.sort((a, b) => a - b)
			.join("-"),
		values,
		code: [
			prefix,
			...values.map((value) => value.meta?.abbr).filter(Boolean),
		].join("-"),
		barcode: generateRandomCode(16, { letters: false, numbers: true }),
		cost_price: 0,
		selling_price: 0,
		description: "",
	}));
};

export const buildPayload = (form) => {
	return {
		code: form.code,
		name: form.name,
		price: form.price,
		description: form.description,
		categories: form.categories.map((category) => category.value),

		has_variants: form.hasVariants,

		skus: form.skus.map((sku) => ({
			id: sku.id ?? null,
			code: sku.code,
			barcode: sku.barcode,
			cost_price: sku.cost_price,
			selling_price: sku.selling_price,
			description: sku.description,
			attributes: sku.values.map((item) => item.value),
		})),
	};
};

export default createResource("/api/products");
