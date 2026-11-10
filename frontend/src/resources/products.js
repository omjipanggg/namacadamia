import createResource from "@/utils/createResource.js";
import i18n from "@/helpers/locale.js";

const SKU_ABBR_CHARS = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
const SKU_ABBR_SPACE = SKU_ABBR_CHARS.length ** 3;

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

// Generate random codes for barcodes and other identifiers.
export const generateRandomCode = (length, options = {}) => {
	const isOptionsObject = typeof options === "object" && options !== null;

	const alpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	const numeric = "0123456789";

	let chars = "";

	if (isOptionsObject ? (options.letters ?? true) : true) {
		chars += alpha;
	}

	if (isOptionsObject ? (options.numbers ?? true) : Boolean(options)) {
		chars += numeric;
	}

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

// Generate a stable identity for an attribute value.
const getValueIdentity = (value, attributeId = value.attribute_id) =>
	value.id != null
		? `id:${value.id}`
		: `new:${attributeId}:${String(value.label ?? "")
				.trim()
				.toLowerCase()}`;

// Normalize an abbreviation to exactly three alphanumeric characters.
const normalizeAbbr = (value) =>
	String(value ?? "")
		.toUpperCase()
		.replace(/[^A-Z0-9]/g, "")
		.slice(0, 3)
		.padEnd(3, "X");

// Generate a random three-character code that isn't already used.
const generateUniqueRandomAbbr = (used) => {
	if (used.size >= SKU_ABBR_SPACE) {
		throw new Error("No more unique three-character SKU codes available.");
	}

	let code;

	do {
		code = Array.from(
			{ length: 3 },
			() => SKU_ABBR_CHARS[Math.floor(Math.random() * SKU_ABBR_CHARS.length)]
		).join("");
	} while (used.has(code));

	return code;
};

// Generate unique abbreviations for one attribute group.
export const generateGroupAbbrs = (values, attributeId) => {
	const result = new Map();
	const used = new Set();

	// Preserve existing abbreviations.
	values.forEach((value) => {
		if (!value.abbr?.trim()) return;

		const abbr = normalizeAbbr(value.abbr);

		if (used.has(abbr)) {
			throw new Error(`Duplicate attribute abbreviation: ${abbr}`);
		}

		used.add(abbr);

		result.set(getValueIdentity(value, attributeId), abbr);
	});

	// Generate abbreviations only for values without one.
	values
		.filter((value) => !value.abbr?.trim())
		.forEach((value) => {
			const abbr = generateUniqueRandomAbbr(used);

			used.add(abbr);

			result.set(getValueIdentity(value, attributeId), abbr);
		});

	return result;
};

// Generate all possible SKU combinations.
export const generateVariants = (groups, prefix) => {
	if (!groups.length) return [];

	// Generate abbreviation maps for every attribute group.
	const abbreviationMaps = new Map(
		groups.map((group) => [
			String(group.attribute.id),
			generateGroupAbbrs(group.values, group.attribute.id),
		])
	);

	// IMPORTANT:
	// Save generated abbreviations directly into the original groups.
	// This prevents new random codes from being generated on subsequent calls.
	groups.forEach((group) => {
		const abbreviationMap = abbreviationMaps.get(String(group.attribute.id));

		group.values.forEach((value) => {
			value.abbr = abbreviationMap.get(
				getValueIdentity(value, group.attribute.id)
			);
		});
	});

	// Generate every possible combination.
	const combinations = groups.reduce(
		(acc, group) => {
			return acc.flatMap((combination) =>
				group.values.map((value) => [
					...combination,
					{
						...value,
						attribute_id: group.attribute.id,
					},
				])
			);
		},
		[[]]
	);

	return combinations.map((values) => {
		const key = values
			.map((value) => getValueIdentity(value))
			.sort()
			.join("|");

		// Use the exact abbreviations stored in the attribute values.
		const suffix = values.map((value) => value.abbr).join("-");

		return {
			key,
			values,
			code: `${prefix}-${suffix}`,
			barcode: generateRandomCode(16),
			cost_price: 0,
			selling_price: 0,
			description: "",
			is_active: true,
		};
	});
};

// Build the API payload.
export const buildPayload = (form) => ({
	code: form.code,
	name: form.name,
	price: form.price,
	description: form.description,

	categories: form.categories.map((category) => category.value),

	has_variants: form.hasVariants,

	attributes: form.selectedAttributes.map((group) => ({
		id: group.attribute.id,
		label: group.attribute.label,

		values: group.values.map((value) => ({
			id: value.id ?? null,
			label: value.label,
			abbr: value.abbr,
		})),
	})),

	skus: form.skus
		.filter((sku) => sku.is_active)
		.map((sku) => ({
			// id: sku.id ?? null,
			// code: sku.code,
			barcode: sku.barcode,
			cost_price: sku.cost_price,
			selling_price: sku.selling_price,
			description: sku.description ?? "",
			is_active: sku.is_active ?? true,

			attributes: sku.values.map((value) => ({
				id: value.id ?? null,
				label: value.label,
				attribute_id: value.attribute_id,
				abbr: value.abbr,
			})),
		})),
});

export default createResource("/api/products");
