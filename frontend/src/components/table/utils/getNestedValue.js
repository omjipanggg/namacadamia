export default function getNestedValue(object, path) {
	if (!path) return object;

	return path.split(".").reduce((current, key) => current?.[key], object);
}
