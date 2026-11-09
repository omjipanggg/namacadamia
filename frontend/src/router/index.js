import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth.js";
import { useToast } from "@/composables/toast.js";
import { useLoadingStore } from "@/stores/loading.js";

import i18n from "@/helpers/locale.js";

const routes = [
	{
		path: "/",
		name: "home.layout",
		redirect: { name: "dashboard.index" },
		component: () => import("@/components/layout/Home.vue"),
		children: [
			{
				path: "",
				name: "home",
				redirect: { name: "dashboard.index" },
				component: () => import("@/views/home/HomeView.vue"),
				meta: {
					title: "navigation.home",
				},
			},
			{
				path: "search",
				name: "search.head",
				children: [
					{
						path: "",
						name: "search.index",
						component: () => import("@/views/home/SearchView.vue"),
						meta: {
							title: "search.title",
						},
					},
				],
			},
			{
				path: "cart",
				name: "cart.index",
				component: () => import("@/views/home/cart/CartView.vue"),
				meta: {
					title: "cart.title",
				},
			},
			{
				path: "products",
				name: "products.head",
				redirect: { name: "products.index" },
				meta: {
					title: "products.title",
				},
				children: [
					{
						path: "",
						name: "products.index",
						component: () => import("@/views/home/product/ProductView.vue"),
					},
					{
						path: ":slug",
						name: "products.show",
						component: () => import("@/views/home/product/ProductShow.vue"),
						meta: {
							title: "products.single",
							routeKey: (route) => `${route.params.slug}`,
						},
					},
				],
			},
			{
				path: "category",
				name: "category.head",
				redirect: { name: "category.index" },
				meta: {
					title: "category.title",
				},
				children: [
					{
						path: "",
						name: "category.index",
						component: () => import("@/views/home/category/CategoryView.vue"),
					},
					{
						path: ":slug",
						name: "category.show",
						component: () => import("@/views/home/category/CategoryShow.vue"),
						meta: {
							title: "category.single",
							routeKey: (route) => `${route.params.slug}`,
						},
					},
				],
			},
			{
				path: "articles",
				name: "blog.head",
				meta: {
					title: "blog.title",
				},
				redirect: {
					name: "blog.index",
				},
				children: [
					{
						path: "",
						name: "blog.index",
						component: () => import("@/views/home/blog/BlogView.vue"),
					},
					{
						path: ":slug",
						name: "blog.show",
						component: () => import("@/views/home/blog/BlogShow.vue"),
						meta: {
							title: "blog.show",
							routeKey: (route) => `${route.params.slug}`,
						},
					},
				],
			},
		],
		meta: {
			title: "navigation.home",
		},
	},
	{
		path: "/dashboard",
		name: "dashboard.layout",
		redirect: { name: "dashboard.index" },
		component: () => import("@/components/layout/Dashboard.vue"),
		children: [
			{
				path: "",
				name: "dashboard.index",
				component: () => import("@/views/dashboard/DashboardView.vue"),
			},
			{
				path: "users",
				name: "dashboard.users",
				component: () => import("@/views/dashboard/users/UsersView.vue"),
				meta: {
					title: "users.title",
				},
			},
			{
				path: "notifications",
				name: "dashboard.notifications",
				component: () =>
					import("@/views/dashboard/notifications/NotificationsView.vue"),
				meta: {
					title: "notifications.title",
				},
			},
			{
				path: "profile",
				name: "dashboard.profile.head",
				children: [
					{
						path: "",
						name: "dashboard.profile",
						component: () => import("@/views/dashboard/users/ProfileView.vue"),
						children: [],
						meta: {
							title: "users.profile",
						},
					},
				],
			},
			{
				path: "settings",
				name: "dashboard.settings.head",
				meta: {
					title: "settings.title",
				},
				redirect: { name: "dashboard.settings" },
				children: [
					{
						path: "",
						name: "dashboard.settings",
						component: () =>
							import("@/views/dashboard/settings/SettingsView.vue"),
					},
					{
						path: "parameters",
						name: "dashboard.parameters",
						component: () =>
							import("@/views/dashboard/settings/ParametersView.vue"),
						meta: {
							title: "parameters.title",
						},
					},
				],
			},
			{
				path: "branches",
				name: "dashboard.branches.head",
				redirect: { name: "dashboard.branches.index" },
				meta: {
					title: "branches.title",
				},
				children: [
					{
						path: "",
						name: "dashboard.branches.index",
						component: () =>
							import("@/views/dashboard/branches/BranchesView.vue"),
					},
				],
			},
			{
				path: "purchases",
				name: "dashboard.purchases.head",
				redirect: {
					name: "dashboard.purchases.index",
				},
				meta: {
					title: "purchases.title",
				},
				children: [
					{
						path: "",
						name: "dashboard.purchases.index",
						component: () =>
							import("@/views/dashboard/purchases/PurchasesView.vue"),
					},
					{
						path: "create",
						name: "dashboard.purchases.create",
						component: () =>
							import("@/views/dashboard/purchases/PurchasesCreate.vue"),
						meta: {
							routeKey: i18n.global.t("purchases.create"),
						},
					},
					{
						path: ":code",
						name: "dashboard.purchases.show",
						component: () =>
							import("@/views/dashboard/purchases/PurchasesShow.vue"),
						meta: {
							routeKey: (route) => `${route.params.code}`,
						},
					},
				],
			},
			{
				path: "tables",
				name: "dashboard.tables.head",
				children: [
					{
						path: "",
						name: "dashboard.tables.index",
						component: () => import("@/views/dashboard/tables/TableView.vue"),
						meta: {
							restricted: true,
							title: "tables.title",
						},
					},
					{
						path: ":slug",
						name: "dashboard.tables.show",
						component: () => import("@/views/dashboard/tables/TableShow.vue"),
						meta: {
							title: "tables.title",
							routeKey: (route) => `${route.params.slug}`,
						},
					},
				],
			},
			{
				path: "products",
				name: "dashboard.products.head",
				redirect: {
					name: "dashboard.purchases.index",
				},
				meta: {
					title: "products.title",
				},
				children: [
					{
						path: "",
						name: "dashboard.products.index",
						component: () =>
							import("@/views/dashboard/products/ProductsView.vue"),
					},
					{
						path: ":code",
						name: "dashboard.products.show",
						component: () =>
							import("@/views/dashboard/products/ProductsShow.vue"),
						meta: {
							title: "products.title",
							routeKey: (route) => `${route.params.slug}`,
						},
					},
				],
			},
			{
				path: "stocks",
				name: "stocks.head",
				redirect: {
					name: "stocks.index",
				},
				meta: {
					title: "stocks.title",
				},
				children: [
					{
						path: "",
						name: "stocks.index",
						component: () => import("@/views/dashboard/stocks/StockView.vue"),
					},
					{
						path: "transfer",
						name: "stocks.transfer",
						component: () =>
							import("@/views/dashboard/stocks/StockTransferView.vue"),
						meta: {
							title: "stocks.transfer",
						},
					},
				],
			},
			{
				path: "sales",
				name: "sales.head",
				redirect: {
					name: "sales.index",
				},
				meta: {
					title: "sales.title",
				},
				children: [
					{
						path: "",
						name: "sales.index",
						component: () => import("@/views/dashboard/sales/SalesView.vue"),
					},
					{
						path: ":code",
						name: "sales.show",
						component: () => import("@/views/dashboard/sales/SalesShow.vue"),
						meta: {
							title: "sales.title",
							routeKey: (route) => `${route.params.code}`,
						},
					},
					{
						path: "create",
						name: "sales.create",
						component: () => import("@/views/dashboard/sales/SalesCreate.vue"),
						meta: {
							title: "sales.title",
							routeKey: i18n.global.t("sales.create"),
						},
					},
				],
			},
			{
				path: "reports",
				name: "dashboard.reports.head",
				meta: {
					title: "dashboard.reports.title",
				},
				redirect: {
					name: "dashboard.reports.sales",
				},
				children: [
					{
						path: "sales",
						name: "dashboard.reports.sales",
						component: () => import("@/views/dashboard/reports/SalesView.vue"),
						meta: {
							title: "dashboard.reports.sales",
						},
					},
					{
						path: "invoices",
						name: "dashboard.reports.invoices",
						component: () =>
							import("@/views/dashboard/reports/InvoicesView.vue"),
						meta: {
							title: "dashboard.reports.invoices",
						},
					},
				],
			},
		],
		meta: {
			title: "dashboard.title",
			requiresAuth: true,
		},
	},
	{
		path: "/",
		name: "auth.layout",
		component: () => import("@/components/layout/Auth.vue"),
		children: [
			{
				path: "login",
				name: "login",
				component: () => import("@/views/auth/LoginView.vue"),
				meta: {
					title: "auth.login",
				},
			},
			{
				path: "register",
				name: "register",
				component: () => import("@/views/auth/RegisterView.vue"),
				meta: {
					title: "auth.register",
				},
			},
		],
		meta: {
			title: "auth",
			restricted: true,
		},
	},
	{
		path: "/bad",
		name: "unknown.head",
		redirect: { name: "unknown.index" },
		component: () => import("@/components/layout/Home.vue"),
		children: [
			{
				path: "",
				name: "unknown.index",
				component: () => import("@/views/miscellaneous/NotFound.vue"),
			},
		],
		meta: {
			title: "navigation.notFound",
		},
	},
	{
		path: "/:pathMatch(.*)*",
		name: "lost.index",
		redirect: { name: "unknown.index" },
		meta: {
			title: "navigation.notFound",
		},
	},
];

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes,
	scrollBehavior() {
		return { top: 0 };
	},
});

router.beforeEach(async (to) => {
	const loadingStore = useLoadingStore();
	const toast = useToast();
	loadingStore.navigationStarted();

	const authStore = useAuthStore();

	if (!authStore.initialized) {
		try {
			await authStore.check();
		} catch (error) {
			authStore.initialized = true;
			throw error;
		}
	}

	if (to.meta.requiresAuth && !authStore.isLoggedIn) {
		return {
			name: "login",
			query: {
				redirect: to.fullPath,
			},
		};
	}

	if (to.meta.restricted && authStore.isLoggedIn && !authStore.isAdmin) {
		toast.danger("common.failure", "errors.access.restricted");
		return {
			name: "dashboard.index",
		};
	}
});

router.afterEach((to) => {
	const loadingStore = useLoadingStore();
	loadingStore.navigationFinished();

	window.scrollTo(0, 0);

	document.title = import.meta.env.VITE_APP_TITLE;

	if (to.meta.title) {
		document.title += ` · ${i18n.global.t(to.meta.title)}`;
	}

	if (to.meta.routeKey) {
		document.title +=
			typeof to.meta.routeKey === "function"
				? `${to.meta.separator ?? " · "}[${to.meta.routeKey(to)}]`
				: ` · ${to.meta.routeKey}`;
	}
});

router.onError(() => {
	window.location.reload();
});

export default router;
