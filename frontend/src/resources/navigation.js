export default [
	{
		label: "navigation.workspace",
		items: [
			{
				key: "dashboard",
				title: "dashboard.title",
				route: "dashboard.index",
				icon: "house",
			},
			{
				key: "notifications",
				title: "notifications.title",
				route: "dashboard.notifications",
				icon: "bell",
			},
		],
	},
	{
		label: "navigation.management",
		items: [
			{
				key: "branches",
				title: "branches.title",
				route: "dashboard.branches.index",
				icon: "buildings",
			},
			{
				key: "products",
				title: "products.title",
				route: "dashboard.products.index",
				icon: "box",
			},
			{
				key: "stocks",
				title: "stocks.title",
				route: "stocks.index",
				icon: "tags",
			},
			{
				key: "users",
				title: "users.title",
				route: "dashboard.users",
				icon: "people",
			},
		],
	},
	{
		label: "navigation.transaction",
		items: [
			{
				key: "purchases",
				title: "purchases.title",
				icon: "truck",
				children: [
					{
						key: "purchases.create",
						title: "purchases.create",
						route: "dashboard.purchases.create",
					},
					{
						key: "purchases.index",
						title: "purchases.view",
						route: "dashboard.purchases.index",
					},
				],
			},
			{
				key: "orders",
				title: "orders.title",
				icon: "handbag",
				children: [
					{
						key: "orders.create",
						title: "orders.create",
						route: "sales.create",
					},
					{
						key: "orders.index",
						title: "orders.view",
						route: "sales.index",
					},
				],
			},
		],
	},
	{
		label: "navigation.reports",
		items: [
			{
				key: "invoices",
				title: "dashboard.reports.invoices",
				route: "dashboard.reports.invoices",
				icon: "file-earmark-ruled",
			},
			{
				key: "sales",
				title: "dashboard.reports.sales",
				route: "dashboard.reports.sales",
				icon: "graph-up-arrow",
			},
		],
	},
	{
		label: "navigation.controls",
		items: [
			{
				key: "parameters",
				title: "parameters.title",
				route: "dashboard.parameters",
				icon: "regex",
			},
			{
				key: "settings",
				title: "settings.title",
				route: "dashboard.settings",
				icon: "gear-wide-connected",
			},
			{
				key: "tables",
				title: "tables.title",
				route: "dashboard.tables.index",
				icon: "table",
				meta: {
					restricted: true,
					visible: false,
				},
			},
			{
				key: "quit",
				title: "navigation.quit",
				icon: "box-arrow-right",
				action: "logout",
			},
		],
	},
];
