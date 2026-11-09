import api from "../services/api.js";

let controller = null;

const locales = {
	messages: {
		en: {
			columns: {
				name: "Name",
				categories: "Categories",
				price: "Price",
				image: "Image",
				description: "Description",
			},
			account: "Account",
			contact: "Contact",
			notifications: {
				title: "Notifications",
				seeAll: "See all notifications",
				mark: "Mark as read",
				markAll: "Mark all as read",
				remove: "Remove this notification",
				sent: "Test function has been sent.",
				empty: "No notifications",
				unread: "Unread",
			},
			dashboard: {
				title: "Dashboard",
				subtitle: "Your all-in-one solutions.",
				index: "Dashboard",
				profile: "Profile",
				users: "Users",
				products: "Products",
				settings: "Settings",
				reports: {
					invoices: "Invoices",
					sales: "Sales",
				},
				notifications: "Notifications",
				tables: "Tables",
			},
			purchases: {
				title: "Purchases",
				detail: "Purchase Detail",
				items: "Purchase Items",
				supplier: "Supplier",
				branch: "Branch",
				notes: "Notes",
			},
			auth: {
				login: "Login",
				register: "Register",
				logout: "Logout",
				password: {
					title: "Password",
					confirm: "Password confirmation",
					show: "Show Password",
					placeholder: "Enter your password",
				},
			},
			checkout: "Checkout",
			notes: "Notes",
			orders: "Orders",
			product: {
				title: "Products",
				index: "Products",
			},
			products: {
				index: "Products",
				title: "Products",
				show: "Products",
			},
			home: "Home",
			why: "Why Choose Us",
			register: "Register",
			signOut: "Sign out",
			signIn: "Sign in",
			profile: {
				index: "Profile",
				title: "Profile",
			},
			settings: {
				title: "Settings",
				index: "Settings",
				appearance: "Appearance",
				theme: "Theme",
				language: "Language",
			},
			cart: {
				title: "Cart",
				items: "Items",
				shopping: "Cart",
				startShopping: "Start shopping now",
				empty: "No items",
			},
			items: {
				title: "Items",
				index: "Items",
				show: "Item",
			},
			locale: {
				switch: "Switch locales",
				switched: "Locale switched successfully",
				current: "Current locale: {locale}",
			},
			options: "Options",
			optional: "Optional",
			configurations: "Configurations",
			theme: {
				toggleDark: "Set dark mode",
				toggleLight: "Set light mode",
			},
			outline: {
				show: "Show outlines",
				hide: "Hide outlines",
			},
			services: "Services",
			time: {
				two: "Two seconds ago",
				now: "just now",
				yesterday: "Yesterday",
				lastUpdated: "Last updated",
			},
			image: "Image",
			career: "Career",
			featured: "Featured",
			feedback: "Feedback",
			explore: "Explore",
			support: "Support",
			users: {
				title: "Users",
			},
			create: "Create",
			read: "Read",
			update: "Update",
			delete: "Delete",
			loginSubtitle: "It only takes a minute to get started.",
			login: "Login",
			loggingIn: "Logging in",
			loginSuccess: "Logged in to {email} account.",
			logout: "Logout",
			logoutSuccess: "You have been logged out.",
			email: "Email address",
			name: "Name",
			rememberMe: "Remember me",
			confirmation: "Confirmation",
			search: {
				index: "Search",
				placeholder: "Search for products, categories, or anything...",
				noResults: "No results found",
				resultsFound: "{count} result(s) found",
				viewAll: "View all results",
				input: "Enter 3 characters or more",
			},
			loading: "Loading",
			category: {
				title: "Category",
				show: "Category",
			},
			blog: {
				index: "Articles",
				title: "Articles",
				show: "Article",
			},
			tables: {
				title: "Tables",
				noData: "No data",
				skus: "SKU",
				sku: "SKU",
				quantity: "Quantity",
				price: "Price",
				total: "Total",
				unit: "Unit",
				costPrice: "Cost Price",
				clickToAddItem: "Click + to add an item",
			},
			processing: "Processing",
			backToHome: "Back to Home",
			areYouLeaving: "Are you really leaving?",
			confirm: "Yes, I am",
			no: "No",
			cancel: "Cancel",
			terms: "Terms & Conditions",
			privacyPolicy: "Privacy Policy",
			workspace: "Workspace",
			management: "Management",
			reports: "Reports",
			controls: "Controls",
			revenue: "Revenue",
			manage: "Manage",
			print: "Print",
			export: "Export",
			exportAsCsv: "Export as CSV",
			exportAsPdf: "Export as PDF",
			pagination: {
				previous: "Previous",
				next: "Next",
				showing: "Showing",
				from: "from",
				to: "to",
				of: "of",
				entries: "entries",
			},
			unknown: {
				index: "404",
			},
			select: {
				placeholder: "Select an option",
				option: "Select a {item}",
				noOptions: "No options available",
				loading: "Loading options...",
				one: "Select one {item}",
			},
			actions: "Actions",
			send: "Send",
			darkMode: "Dark mode",
			allCaughtUp: "All caught up!",
			addNew: "New {item}",
			save: "Save",
		},
	},
	id: {
		columns: {
			name: "Nama",
			categories: "Kategori",
			price: "Harga",
			image: "Foto",
			description: "Deskripsi",
		},
		account: "Akun",
		contact: "Kontak",
		notifications: {
			title: "Pemberitahuan",
			seeAll: "Lihat semua pemberitahuan",
			mark: "Tandai dibaca",
			markAll: "Tandai semua dibaca",
			remove: "Hapus pemberitahuan ini",
			sent: "Pemberitahuan dikirm.",
			empty: "Tidak ada pemberitahuan",
			unread: "Belum dibaca",
		},
		dashboard: {
			title: "Dasbor",
			subtitle: "Namanya beda.",
			index: "Dasbor",
			profile: "Profil",
			users: "Pengguna",
			products: "Produk",
			settings: "Pengaturan",
			reports: {
				invoices: "Faktur",
				sales: "Penjualan",
			},
			notifications: "Pemberitahuan",
			tables: "Tabel",
		},

		purchases: {
			title: "Pembelian",
			detail: "Rincian Pembelian",
			items: "Barang Pembelian",
			supplier: "Supplier",
			branch: "Cabang",
			notes: "Catatan",
		},
		auth: {
			login: "Masuk",
			register: "Daftar",
			logout: "Keluar",
			password: {
				title: "Password",
				confirm: "Password confirmation",
				show: "Show Password",
				placeholder: "Enter your password",
			},
		},
		checkout: "Pembayaran",
		notes: "Catatan",
		orders: "Pesanan",
		revenue: "Penghasilan",
		product: {
			title: "Produk",
			index: "Produk",
		},
		products: {
			index: "Produk",
			title: "Produk",
			show: "Produk",
		},
		home: "Beranda",
		why: "Layanan Kami",
		register: "Daftar",
		signOut: "Keluar",
		signIn: "Masuk",
		profile: {
			index: "Profil",
			title: "Profil",
		},
		settings: {
			title: "Pengaturan",
			index: "Pengaturan",
			appearance: "Tampilan",
			theme: "Tema",
			language: "Bahasa",
		},
		cart: {
			title: "Keranjang",
			items: "Barang",
			shopping: "Keranjang",
			startShopping: "Mulai berbelanja sekarang",
			empty: "Keranjang kosong",
		},
		locale: {
			switch: "Ganti bahasa",
			switched: "Bahasa berhasil diubah",
			current: "Bahasa saat ini: {locale}",
		},
		users: {
			title: "Pengguna",
		},
		create: "Tambah",
		read: "Baca",
		update: "Perbarui",
		delete: "Hapus",
		options: "Opsi",
		optional: "Opsional",
		configurations: "Konfigurasi",
		theme: {
			toggleDark: "Mode gelap",
			toggleLight: "Mode terang",
		},
		outline: {
			hide: "Hilangkan garis",
			show: "Tampilkan garis",
		},
		services: "Layanan",
		time: {
			two: "Sedetik yang lalu",
			now: "baru saja",
			yesterday: "Kemarin",
			lastUpdated: "Terakhir diperbarui",
		},
		image: "Gambar",
		career: "Karir",
		featured: "Unggulan",
		feedback: "Laporkan",
		explore: "Terlusuri",
		support: "Dukungan",
		loginSubtitle: "Masukkan kredensial untuk melanjutkan.",
		login: "Masuk",
		loggingIn: "Memasukkan",
		loginSuccess: "Berhasil masuk ke akun {email}.",
		logout: "Keluar",
		logoutSuccess: "Berhasil keluar dari akun.",
		email: "Alamat email",
		name: "Nama",
		rememberMe: "Ingat saya",
		confirmation: "Konfirmasi",
		search: {
			index: "Pencarian",
			placeholder: "Cari produk, kategori, atau apapun...",
			noResults: "Tidak ada hasil yang ditemukan",
			resultsFound: "{count} hasil ditemukan",
			viewAll: "Lihat semua hasil",
			input: "Masukkan 3 karakter atau lebih",
		},
		loading: "Memuat",
		category: {
			title: "Kategori",
			show: "Kategori",
		},
		blog: {
			index: "Artikel",
			title: "Artikel",
			show: "Artikel",
		},
		tables: {
			noData: "Tidak ada data",
			title: "Tabel",
			skus: "SKU",
			sku: "SKU",
			quantity: "Kuantitas",
			price: "Harga",
			total: "Total",
			unit: "Satuan",
			costPrice: "Harga Pokok",
			clickToAddItem: "Tekan [+] untuk menambahkan barang",
		},
		processing: "Memproses",
		backToHome: "Kembali ke Beranda",
		areYouLeaving: "Yakin keluar?",
		confirm: "Ya",
		no: "Tidak",
		cancel: "Batal",
		terms: "Syarat & Ketentuan",
		privacyPolicy: "Kebijakan Privasi",
		workspace: "Panel",
		management: "Manajemen",
		reports: "Laporan",
		controls: "Kontrol",
		manage: "Kelola",
		print: "Cetak",
		export: "Ekspor",
		exportAsCsv: "Ekspor ke CSV",
		exportAsPdf: "Ekspor ke PDF",
		pagination: {
			previous: "Sebelumnya",
			next: "Selanjutnya",
			showing: "Menampilkan",
			from: "mulai",
			to: "sampai",
			of: "dari",
			entries: "data",
		},
		unknown: {
			index: "404",
		},
		actions: "Aksi",
		send: "Kirim",
		darkMode: "Mode gelap",
		allCaughtUp: "Beneran gak ada",
		addNew: "Tambah {item}",
		save: "Simpan",
	},
};

watchDebounced(
    () => filters.search,
    () => {
        filters.page = 1;
        fetchSales();
    },
    { debounce: 480 }
);

watch(
    () => [filters.status, filters.branch_id, filters.sales_channel_id],
    () => {
        filters.page = 1;
        fetchSales();
    }
);

export default {
	async index(url, params) {
		if (controller) {
			controller.abort();
		}

		const currentController = new AbortController();
		controller = currentController;

		try {
			return await api.get(
				url,
				{ params },
				{ signal: currentController.signal }
			);
		} finally {
			if (controller === currentController) {
				controller = null;
			}
		}

		return;
	},
	async show(id) {},
	async store(data) {},
	async update(id, data) {},
	async destroy(id) {},
};

// themeStore.js
import { ref, watch } from "vue";
import { defineStore } from "pinia";

export const useThemeStore = defineStore("theme", () => {
	const darkMode = ref(localStorage.getItem("theme") === "dark");

	const toggleTheme = () => {
		darkMode.value = !darkMode.value;
	};

	watch(
		darkMode,
		(value) => {
			localStorage.setItem("theme", value ? "dark" : "light");
			document.documentElement.setAttribute(
				"data-bs-theme",
				value ? "dark" : "light"
			);
		},
		{ immediate: true }
	);

	return {
		darkMode,
		toggleTheme,
	};
});

// PROFILE
const profile = {
	key: "profile",
	title: "dashboard.profile",
	route: "dashboard.profile",
	icon: "person",
};

{
    
    attributeById: (state) => (id) =>
        state.product_attributes.find((item) => item.value === id),
    attributeValues: (state) => (id) =>
        state.product_attribute_values.filter(
            (value) => value.meta.product_attribute_id === id
        ),
		categoryById: (state) => (id) =>
			state.product_categories.find((category) => category.value === id),
    }