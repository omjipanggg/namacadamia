import createResource from "../utils/createResource.js";

export const columns = [
	{
		key: "name",
		label: "Name",
	},
	{
		key: "email",
		label: "Email",
	},
	{
		key: "branches",
		label: "Branches",
		displayKey: "name",
	},
	{
		key: "roles",
		label: "Roles",
		displayKey: "name",
	},
	{
		key: "is_active",
		label: "Status",
	},
];

export default createResource("/api/users");
