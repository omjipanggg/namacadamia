// import i18n from "@/helpers/locale.js";
import { formatMoney } from "@/helpers/format.js";

export const saleStatus = [
	"all",
	"pending",
	"partial",
	"paid",
	"completed",
	"cancelled",
];

export const columns = [
	{
		key: "code",
		label: "Code",
		sortable: true,
	},
	{
		key: "branch.name",
		label: "Branch",
	},
	{
		key: "channel.name",
		label: "Channel",
	},
	{
		key: "subtotal",
		label: "Total",
		formatter: (row) => formatMoney(row.subtotal),
	},
	{
		key: "created_at",
		label: "Order Date",
		sortable: true,
	},
	{
		key: "status",
		label: "Status",
	},
	{
		key: "actions",
		label: "",
	},
];
