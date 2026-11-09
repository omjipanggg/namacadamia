import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";
import path from "node:path";

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
	const env = loadEnv(mode, process.cwd(), "");
	return {
		server: {
			host: true,
		},
		plugins: [vue()],
		base: env.VITE_BASE_URL || "/",
		resolve: {
			alias: [
				{
					find: /^~(.*)$/,
					replacement: "$1",
				},
				{
					find: "@/",
					replacement: `${path.resolve(__dirname, "src")}/`,
				},
				{
					find: "@",
					replacement: path.resolve(__dirname, "/src"),
				},
			],
			extensions: [
				".mjs",
				".js",
				".ts",
				".jsx",
				".tsx",
				".json",
				".vue",
				".scss",
			],
		},
	};
});
