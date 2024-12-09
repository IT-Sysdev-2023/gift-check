import globals from "globals";
import pluginJs from "@eslint/js";
import tseslint from "typescript-eslint";
import pluginVue from "eslint-plugin-vue";
import eslintConfigPrettier from "eslint-config-prettier";

/** @type {import('eslint').Linter.Config[]} */
export default [
    { files: ["**/*.{js,mjs,cjs,ts,vue}"] },
    {
        languageOptions: { globals: { ...globals.browser, route: "readonly" } },
    },
    pluginJs.configs.recommended,
    ...tseslint.configs.recommended,
    ...pluginVue.configs["flat/essential"],
    {
        files: ["**/*.vue"],
        languageOptions: { parserOptions: { parser: tseslint.parser } },
    },
    eslintConfigPrettier,

    //Ignore MultiWord Component
    {
        files: [
            "resources/js/Pages/Treasury/Adjustment/Allocation.vue",
            "resources/js/Pages/Treasury/Table.vue",
        ], // Target the specific file
        rules: {
            "vue/multi-word-component-names": "off", // Disable the rule for this file
        },
    },
];
