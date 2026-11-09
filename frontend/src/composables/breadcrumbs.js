export const generateBreadcrumbs = (route) => {
	const crumbs = [];

	for (const record of route.matched) {
		if (!record.name) continue;

		let label = "";

		if (record.meta?.routeKey) {
			label =
				typeof record.meta.routeKey === "function"
					? record.meta.routeKey(route)
					: record.meta.routeKey;
		} else if (record.meta?.title) {
			label =
				typeof record.meta.title === "function"
					? record.meta.title(route)
					: record.meta.title;
		}

		if (!label) continue;

		let targetRouteName = record.name;
		if (record.name === "dashboard.layout") {
			targetRouteName = "dashboard.index";
		} else if (record.name === "dashboard.purchases.head") {
			targetRouteName = "dashboard.purchases.index";
		}

		const validParams = {};
		if (record.keys && record.keys.length > 0) {
			record.keys.forEach((key) => {
				if (route.params[key.name]) {
					validParams[key.name] = route.params[key.name];
				}
			});
		}

		crumbs.push({
			label,
			route: {
				name: targetRouteName,
				params: validParams,
			},
		});
	}

	return crumbs;
};
