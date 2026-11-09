import js from "@eslint/js"
import vue from "eslint-plugin-vue"
import globals from "globals"
import eslintConfigPrettier from "eslint-config-prettier"

export default [
  js.configs.recommended,
  ...vue.configs["flat/recommended"],

  {
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node,
      },
    },

    rules: {
      "no-unused-vars": "warn",
      "no-console": "off",
    },
  },

  eslintConfigPrettier,
]
