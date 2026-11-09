export const columns = [
	{
		key: "code",
		label: "Code",
		sortable: true,
	},
	{
		key: "name",
		label: "Name",
		sortable: true,
	},
	{
		key: "stocks_count",
		label: "SKU",
	},
	{
		key: "stocks_sum_quantity",
		label: "Stock",
		formatter: (row) => (row.stocks_sum_quantity ? row.stocks_sum_quantity : 0),
	},
	{
		key: "branch.sales_count",
		label: "Sales",
		formatter: (row) => (row.branch ? row.branch.sales_count : 0),
	},
	{
		key: "purchases_count",
		label: "Purchases",
	},
	{
		key: "branch.users_count",
		label: "Users",
		formatter: (row) => (row.branch ? row.branch.users_count : 0),
	},
	{
		key: "is_active",
		label: "Status",
	},
];
